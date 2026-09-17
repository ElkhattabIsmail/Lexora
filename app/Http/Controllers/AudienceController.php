<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAudienceRequest;
use App\Http\Requests\UpdateAudienceRequest;
use App\Models\Audience;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AudienceController — manages court hearings nested under a Dossier.
 *
 * All routes are nested under /dossiers/{dossier}/audiences/
 * and are protected by auth + verified + role:Avocat,Administrateur.
 * The 'index' and 'show' actions are excluded (hearings are shown on the dossier page).
 */
class AudienceController extends Controller
{
    /**
     * Shows the form to schedule a new hearing for a given dossier.
     *
     * @param  Dossier  $dossier  Route-model-bound dossier this hearing belongs to.
     *
     * @return View  audiences.create  with: $dossier, $avocats
     */
    public function create(Dossier $dossier): View
    {
        $avocats = User::avocats()->get();

        return view('audiences.create', compact('dossier', 'avocats'));
    }

    /**
     * Validates and creates a new Audience for the given dossier, then logs the action.
     *
     * @param  StoreAudienceRequest  $request  Validated hearing data.
     * @param  Dossier               $dossier  The parent dossier to attach the hearing to.
     *
     * Steps:
     *   1. Validate input via StoreAudienceRequest.
     *   2. Create the Audience record linked to $dossier.
     *   3. Log "Audience planifiée" to the dossier's historique.
     *   4. Redirect back to the dossier show page.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function store(StoreAudienceRequest $request, Dossier $dossier): RedirectResponse
    {
        $audience = $dossier->audiences()->create($request->validated());

        $dossier->enregistrerAction(
            "Audience planifiée : {$audience->tribunal} le {$audience->date->format('d/m/Y')} à {$audience->heure}",
            $request->user(),
        );

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été planifiée avec succès.');
    }

    /**
     * Shows the form to edit an existing hearing.
     *
     * @param  Dossier   $dossier   Parent dossier (route binding, used for URL generation).
     * @param  Audience  $audience  Route-model-bound audience to edit.
     *
     * @return View  audiences.edit  with: $dossier, $audience, $avocats
     */
    public function edit(Dossier $dossier, Audience $audience): View
    {
        $avocats = User::avocats()->get();

        return view('audiences.edit', compact('dossier', 'audience', 'avocats'));
    }

    /**
     * Validates and saves changes to an existing hearing, then logs the action.
     *
     * @param  UpdateAudienceRequest  $request   Validated update payload.
     * @param  Dossier                $dossier   Parent dossier (for logging and redirect).
     * @param  Audience               $audience  The hearing to update.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function update(UpdateAudienceRequest $request, Dossier $dossier, Audience $audience): RedirectResponse
    {
        $audience->update($request->validated());

        $dossier->enregistrerAction(
            "Audience mise à jour : {$audience->tribunal} ({$audience->statut})",
            $request->user(),
        );

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été mise à jour.');
    }

    /**
     * Logs the deletion action then removes the hearing.
     * Note: the log is written BEFORE deletion so the dossier still exists.
     *
     * @param  Request   $request   Used to retrieve the authenticated user for the log.
     * @param  Dossier   $dossier   Parent dossier (for logging and redirect).
     * @param  Audience  $audience  The hearing to delete.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function destroy(Request $request, Dossier $dossier, Audience $audience): RedirectResponse
    {
        $dossier->enregistrerAction(
            "Audience supprimée : {$audience->tribunal}",
            $request->user(),
        );

        $audience->delete();

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été supprimée.');
    }
}
