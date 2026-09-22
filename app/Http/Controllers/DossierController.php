<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDossierRequest;
use App\Http\Requests\UpdateDossierRequest;
use App\Jobs\SupprimerDocumentsDossier;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use App\Traits\GeneratesSequentialReference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DossierController — manages CRUD operations for legal case files.
 *
 * Routes (all protected by auth + verified + role:Avocat,Administrateur):
 *   GET    /dossiers              → index()
 *   GET    /dossiers/create       → create()
 *   POST   /dossiers              → store()
 *   GET    /dossiers/{dossier}    → show()
 *   GET    /dossiers/{id}/edit    → edit()
 *   PATCH  /dossiers/{id}         → update()
 *   DELETE /dossiers/{id}         → destroy()
 *
 * Uses the GeneratesSequentialReference trait to auto-number new dossiers.
 */
class DossierController extends Controller
{
    /**
     * Shared trait that provides generateSequentialReference().
     * @see GeneratesSequentialReference::generateSequentialReference()
     */
    use GeneratesSequentialReference;

    /**
     * Displays the paginated list of dossiers with optional filters.
     *
     * Query string parameters:
     *   @param  string  $statut    (optional) Filter by status: "En cours", "Gagné", "Perdu", "Fermé".
     *   @param  int     $avocat_id (optional) Filter to dossiers assigned to a specific lawyer.
     *   @param  string  $search    (optional) Free-text search (number, type, or client name).
     *
     * @return View  dossiers.index  with: $dossiers (paginated), $avocats, $statut, $avocatId, $search
     *
     * Similar: ClientController::index(), FactureController::index() — same filter+paginate pattern.
     */
    public function index(Request $request): View
    {
        $statut = $request->string('statut')->trim()->toString();
        $avocatId = $request->integer('avocat_id') ?: null;
        $search = $request->string('search')->trim()->toString();

        $dossiers = Dossier::query()
            ->with(['client', 'avocat'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($avocatId, fn ($q) => $q->where('avocat_id', $avocatId))
            ->when($search, fn ($q) => $q->recherche($search))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $avocats = User::avocats()->get();

        return view('dossiers.cv', compact('dossiers', 'avocats', 'statut', 'avocatId', 'search'));
    }

    /**
     * Shows the form to create a new dossier.
     * Pre-loads the list of clients (alphabetical) and avocats (alphabetical)
     * to populate the select dropdowns.
     *
     * @return View  dossiers.create  with: $clients, $avocats
     *
     * Similar: AudienceController::create(), FactureController::create()
     */
    public function create(): View
    {
        $clients = Client::orderBy('nom')->get();
        $avocats = User::avocats()->get();

        return view('dossiers.create', compact('clients', 'avocats'));
    }

    /**
     * Validates the form submission, creates a new Dossier, and logs the action.
     *
     * @param  StoreDossierRequest  $request  Validated form data (see rules in StoreDossierRequest).
     *
     * Steps:
     *   1. Validate input via StoreDossierRequest.
     *   2. Auto-generate the reference number e.g. "DOS-2024-00001".
     *   3. Create the Dossier record.
     *   4. Log "Dossier ouvert" in the historique.
     *   5. Redirect to the show page with a success flash.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     *
     * Similar: FactureController::store() — same auto-reference + log pattern.
     */
    public function store(StoreDossierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['numero_dossier'] = $this->generateSequentialReference(Dossier::class, 'DOS');

        $dossier = Dossier::create($data);
        $dossier->enregistrerAction("Dossier ouvert : {$dossier->numero_dossier}", $request->user());

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le dossier a été créé avec succès.');
    }

    /**
     * Displays a detailed view of a single dossier.
     * Eager-loads all related data to avoid N+1 queries on the show page.
     *
     * @param  Dossier  $dossier  Route-model-bound dossier instance.
     *
     * Loaded relations: client, avocat, audiences.avocat, documents.uploader,
     *                   factures, historiques.user
     *
     * @return View  dossiers.show  with: $dossier
     */
    public function show(Dossier $dossier): View
    {
        $dossier->load([
            'client',
            'avocat',
            'audiences.avocat',
            'documents.uploader',
            'factures',
            'historiques.user',
        ]);

        return view('dossiers.show', compact('dossier'));
    }

    /**
     * Shows the form to edit an existing dossier.
     * Pre-loads clients and avocats for the select dropdowns.
     *
     * @param  Dossier  $dossier  Route-model-bound dossier instance.
     *
     * @return View  dossiers.edit  with: $dossier, $clients, $avocats
     */
    public function edit(Dossier $dossier): View
    {
        $clients = Client::orderBy('nom')->get();
        $avocats = User::avocats()->get();

        return view('dossiers.edit', compact('dossier', 'clients', 'avocats'));
    }

    /**
     * Validates and applies changes to an existing dossier.
     * If the status changed, a new historique entry is automatically logged.
     *
     * @param  UpdateDossierRequest  $request  Validated update payload.
     * @param  Dossier               $dossier  Route-model-bound dossier to update.
     *
     * Steps:
     *   1. Validate input via UpdateDossierRequest.
     *   2. Merge the boolean 'archive' flag from the request.
     *   3. Persist the update.
     *   4. If statut changed, log "Statut modifié : old → new" to historique.
     *   5. Redirect to the show page with a success flash.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function update(UpdateDossierRequest $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validated();
        $data['archive'] = $request->boolean('archive');

        $statutAvant = $dossier->statut;
        $dossier->update($data);

        if ($statutAvant !== $dossier->statut) {
            $dossier->enregistrerAction(
                "Statut modifié : {$statutAvant} → {$dossier->statut}",
                $request->user(),
            );
        }

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le dossier a été mis à jour.');
    }

    /**
     * Deletes a dossier and dispatches a background job to clean up its files.
     *
     * @param  Dossier  $dossier  Route-model-bound dossier instance to delete.
     *
     * Steps:
     *   1. Capture the dossier ID before deletion (needed for the job).
     *   2. Delete the Dossier (cascades to audiences, historiques, etc. via DB constraints).
     *   3. Dispatch SupprimerDocumentsDossier to remove stored files asynchronously.
     *   4. Redirect to the list with a success flash.
     *
     * @return RedirectResponse  Redirects to dossiers.index.
     *
     * Similar: ClientController::destroy(), FactureController::destroy()
     */
    public function destroy(Dossier $dossier): RedirectResponse
    {
        $dossierId = $dossier->id;

        $dossier->delete();
        SupprimerDocumentsDossier::dispatch($dossierId);

        return redirect()
            ->route('dossiers.index')
            ->with('success', 'Le dossier a été supprimé.');
    }
}
