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
    public function create(Dossier $dossier): View
    {
        // ---------------------------------------------------------------------
        // Scope local User::avocats() : ne récupère que les utilisateurs ayant le rôle 'Avocat'.
        // Permet d'assigner un avocat spécifique à cette audience.
        // ---------------------------------------------------------------------
        $avocats = User::avocats()->get();

        // ---------------------------------------------------------------------
        // Affiche le formulaire de planification rattaché au dossier parent.
        // ---------------------------------------------------------------------
        return view('audiences.create', compact('dossier', 'avocats'));
    }

    public function store(StoreAudienceRequest $request, Dossier $dossier): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // $dossier->audiences()->create(...) :
        // Crée l'audience directement liée au dossier parent via la relation HasMany
        // en assignant automatiquement la clé étrangère 'dossier_id'.
        // ---------------------------------------------------------------------
        $audience = $dossier->audiences()->create($request->validated());

        // ---------------------------------------------------------------------
        // Journalise la planification de l'audience dans l'historique d'audit du dossier.
        // ---------------------------------------------------------------------
        $dossier->enregistrerAction(
            "Audience planifiée : {$audience->tribunal} le {$audience->date->format('d/m/Y')} à {$audience->heure}",
            $request->user(),
        );

        // ---------------------------------------------------------------------
        // Redirection vers la page du dossier avec message de confirmation.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été planifiée avec succès.');
    }

    public function edit(Dossier $dossier, Audience $audience): View
    {
        // ---------------------------------------------------------------------
        // Récupère la liste des avocats pour permettre de réassigner l'audience si besoin.
        // ---------------------------------------------------------------------
        $avocats = User::avocats()->get();

        // ---------------------------------------------------------------------
        // Affiche le formulaire d'édition de l'audience avec les données existantes.
        // ---------------------------------------------------------------------
        return view('audiences.edit', compact('dossier', 'audience', 'avocats'));
    }

    public function update(UpdateAudienceRequest $request, Dossier $dossier, Audience $audience): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Met à jour les champs de l'audience (date, heure, tribunal, statut, etc.).
        // ---------------------------------------------------------------------
        $audience->update($request->validated());

        // ---------------------------------------------------------------------
        // Trace la modification de l'audience dans l'historique du dossier.
        // ---------------------------------------------------------------------
        $dossier->enregistrerAction(
            "Audience mise à jour : {$audience->tribunal} ({$audience->statut})",
            $request->user(),
        );

        // ---------------------------------------------------------------------
        // Redirection vers le dossier parent.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été mise à jour.');
    }

    public function destroy(Request $request, Dossier $dossier, Audience $audience): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Trace la suppression dans l'historique AVANT de supprimer l'audience,
        // garantissant que les informations du tribunal soient conservées dans les logs.
        // ---------------------------------------------------------------------
        $dossier->enregistrerAction(
            "Audience supprimée : {$audience->tribunal}",
            $request->user(),
        );

        // ---------------------------------------------------------------------
        // Suppression définitive de la ligne en base de données.
        // ---------------------------------------------------------------------
        $audience->delete();

        // ---------------------------------------------------------------------
        // Redirection vers le dossier avec message flash.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été supprimée.');
    }
}
