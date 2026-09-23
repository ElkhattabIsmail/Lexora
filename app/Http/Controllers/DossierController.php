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
        // ---------------------------------------------------------------------
        // Récupération et assainissement des paramètres passés dans l'URL (?statut=...&avocat_id=...&search=...)
        // ---------------------------------------------------------------------
        $statut = $request->string('statut')->trim()->toString();
        $avocatId = $request->integer('avocat_id') ?: null;
        $search = $request->string('search')->trim()->toString();


        // ---------------------------------------------------------------------
        // Construction dynamique de la requête Eloquent :
        // ---------------------------------------------------------------------
        $dossiers = Dossier::query()
            // -----------------------------------------------------------------
            // with(['client', 'avocat']) :
            // Eager loading des relations pour charger tous les clients et avocats liés
            // en seulement 2 requêtes SQL supplémentaires au lieu de requêtes N+1.
            // -----------------------------------------------------------------
            ->with(['client', 'avocat'])
            // -----------------------------------------------------------------
            // when($condition, Closure) :
            // N'applique le filtre SQL WHERE que si la variable $statut est non vide.
            // -----------------------------------------------------------------
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            // -----------------------------------------------------------------
            // when($condition, Closure) :
            // Filtre sur la clé étrangère avocat_id si un avocat spécifique a été sélectionné.
            // -----------------------------------------------------------------
            ->when($avocatId, fn ($q) => $q->where('avocat_id', $avocatId))
            // -----------------------------------------------------------------
            // scopeRecherche($search) :
            // Applique le scope local de recherche multicritère (numéro, type d'affaire, client).
            // -----------------------------------------------------------------
            ->when($search, fn ($q) => $q->recherche($search))
            // -----------------------------------------------------------------
            // latest() : Raccourci Eloquent équivalent à orderBy('created_at', 'desc').
            // -----------------------------------------------------------------
            ->latest()
            // -----------------------------------------------------------------
            // paginate(15) : Découpe les résultats par lots de 15 éléments par page.
            // -----------------------------------------------------------------
            ->paginate(15)
            // -----------------------------------------------------------------
            // withQueryString() : Conserve automatiquement les filtres de recherche dans les liens de pagination (?page=2&search=...).
            // -----------------------------------------------------------------
            ->withQueryString();

        // ---------------------------------------------------------------------
        // User::avocats() :
        // Scope local sur le modèle User qui ne filtre que les utilisateurs ayant le rôle "Avocat".
        // Alimente la liste déroulante du filtre de recherche dans la vue.
        // ---------------------------------------------------------------------
        $avocats = User::avocats()->get();

/*                         dd($search);
 */

        // ---------------------------------------------------------------------
        // Retourne la vue Blade 'resources/views/dossiers/index.blade.php' avec les variables compactées.
        // ---------------------------------------------------------------------
        return view('dossiers.index', compact('dossiers', 'avocats', 'statut', 'avocatId', 'search'));
    }

    public function create(): View
    {
        // ---------------------------------------------------------------------
        // Récupère la liste alphabétique de tous les clients pour le <select> du formulaire.
        // ---------------------------------------------------------------------
        $clients = Client::orderBy('nom')->get();

        // ---------------------------------------------------------------------
        // Récupère la liste des utilisateurs ayant le rôle 'Avocat' pour l'assignation du dossier.
        // ---------------------------------------------------------------------
        $avocats = User::avocats()->get();

        // ---------------------------------------------------------------------
        // Affiche le formulaire de saisie : 'resources/views/dossiers/create.blade.php'
        // ---------------------------------------------------------------------
        return view('dossiers.create', compact('clients', 'avocats'));
    }

    public function store(StoreDossierRequest $request): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // request->validated() :
        // Ne récupère que les données validées selon les règles strictes de StoreDossierRequest.
        // ---------------------------------------------------------------------
        $data = $request->validated();

        // ---------------------------------------------------------------------
        // generateSequentialReference(...) :
        // Méthode du trait GeneratesSequentialReference qui calcule le numéro annuel unique (ex: DOS-2026-00001).
        // ---------------------------------------------------------------------
        $data['numero_dossier'] = $this->generateSequentialReference(Dossier::class, 'DOS');

        // ---------------------------------------------------------------------
        // Dossier::create(...) :
        // Insère l'enregistrement dans la table 'dossiers' en utilisant le mass assignment ($fillable).
        // ---------------------------------------------------------------------
        $dossier = Dossier::create($data);

        // ---------------------------------------------------------------------
        // enregistrerAction(...) :
        // Journalise l'événement d'ouverture dans la table 'historiques' avec l'utilisateur connecté ($request->user()).
        // ---------------------------------------------------------------------
        $dossier->enregistrerAction("Dossier ouvert : {$dossier->numero_dossier}", $request->user());

        // ---------------------------------------------------------------------
        // Redirection vers la vue de détail du dossier fraîchement créé avec message flash de succès.
        // ---------------------------------------------------------------------

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le dossier a été créé avec succès.');
    }

    public function show(Dossier $dossier): View
    {
        // ---------------------------------------------------------------------
        // load(...) :
        // Eager loading à la demande (Lazy Eager Loading) sur l'instance injectée par le Route Model Binding.
        // Charge en mémoire les relations directes et imbriquées (audiences.avocat, documents.uploader, etc.).
        // ---------------------------------------------------------------------
        $dossier->load([
            'client',
            'avocat',
            'audiences.avocat',
            'documents.uploader',
            'factures',
            'historiques.user',
        ]);

        // ---------------------------------------------------------------------
        // Affiche la vue 'resources/views/dossiers/show.blade.php' contenant les onglets détaillés.
        // ---------------------------------------------------------------------
        return view('dossiers.show', compact('dossier'));
    }

    public function edit(Dossier $dossier): View
    {
        // ---------------------------------------------------------------------
        // Prépare les données nécessaires pour alimenter les listes déroulantes de modification.
        // ---------------------------------------------------------------------
        $clients = Client::orderBy('nom')->get();
        $avocats = User::avocats()->get();

        // ---------------------------------------------------------------------
        // Affiche le formulaire d'édition prérempli avec les données actuelles de $dossier.
        // ---------------------------------------------------------------------
        return view('dossiers.edit', compact('dossier', 'clients', 'avocats'));
    }

    public function update(UpdateDossierRequest $request, Dossier $dossier): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Données validées via UpdateDossierRequest.
        // ---------------------------------------------------------------------
        $data = $request->validated();

        // ---------------------------------------------------------------------
        // Convertit la valeur de la case à cocher 'archive' en booléen strict (true/false).
        // ---------------------------------------------------------------------
        $data['archive'] = $request->boolean('archive');

        // ---------------------------------------------------------------------
        // Mémorise le statut avant mise à jour pour détecter tout changement d'état.
        // ---------------------------------------------------------------------
        $statutAvant = $dossier->statut;

        // ---------------------------------------------------------------------
        // Enregistre les modifications en base de données.
        // ---------------------------------------------------------------------
        $dossier->update($data);

        // ---------------------------------------------------------------------
        // Détection de transition d'état :
        // Si le statut a changé (ex: "En cours" -> "Gagné"), on consigne ce changement dans l'historique d'audit.
        // ---------------------------------------------------------------------
        if ($statutAvant !== $dossier->statut) {
            $dossier->enregistrerAction(
                "Statut modifié : {$statutAvant} → {$dossier->statut}",
                $request->user(),
            );
        }

        // ---------------------------------------------------------------------
        // Redirection vers la page du dossier avec confirmation de mise à jour.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le dossier a été mis à jour.');
    }

    public function destroy(Dossier $dossier): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Mémorise l'ID numérique avant suppression de l'enregistrement Eloquent.
        // ---------------------------------------------------------------------
        $dossierId = $dossier->id;

        // ---------------------------------------------------------------------
        // Supprime l'enregistrement dans la table 'dossiers'.
        // Grâce aux clés étrangères ON DELETE CASCADE, les audiences et historiques liés sont supprimés par le SGBD.
        // ---------------------------------------------------------------------
        $dossier->delete();

        // ---------------------------------------------------------------------
        // SupprimerDocumentsDossier::dispatch(...) :
        // Déclenche un Job asynchrone dans la file d'attente (Queue)
        // pour supprimer physiquement tous les fichiers du dossier sur le disque sans bloquer la requête HTTP.
        // ---------------------------------------------------------------------
        SupprimerDocumentsDossier::dispatch($dossierId);

        // ---------------------------------------------------------------------
        // Redirection vers la liste des dossiers avec notification flash.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.index')
            ->with('success', 'Le dossier a été supprimé.');
    }
}
