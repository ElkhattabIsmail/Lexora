<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAudienceRequest;
use App\Http\Requests\UpdateAudienceRequest;
use App\Models\Audience;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AudienceController extends Controller
{
    public function create(Dossier $dossier): View
    {
        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();

        return view('audiences.create', compact('dossier', 'avocats'));
    }

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

    public function edit(Dossier $dossier, Audience $audience): View
    {
        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();

        return view('audiences.edit', compact('dossier', 'audience', 'avocats'));
    }

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

    public function destroy(Dossier $dossier, Audience $audience): RedirectResponse
    {
        $dossier->enregistrerAction(
            "Audience supprimée : {$audience->tribunal}",
            request()->user(),
        );

        $audience->delete();

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'L\'audience a été supprimée.');
    }
}
