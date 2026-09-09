<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDossierRequest;
use App\Http\Requests\UpdateDossierRequest;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DossierController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->string('statut')->trim()->toString();
        $avocatId = $request->integer('avocat_id') ?: null;

        $dossiers = Dossier::query()
            ->with(['client', 'avocat'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($avocatId, fn ($q) => $q->where('avocat_id', $avocatId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();

        return view('dossiers.index', compact('dossiers', 'avocats', 'statut', 'avocatId'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('nom')->get();
        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();

        return view('dossiers.create', compact('clients', 'avocats'));
    }

    public function store(StoreDossierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['numero_dossier'] = $this->generateNumeroDossier();

        $dossier = Dossier::create($data);
        $dossier->enregistrerAction("Dossier ouvert : {$dossier->numero_dossier}", $request->user());

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le dossier a été créé avec succès.');
    }

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

    public function edit(Dossier $dossier): View
    {
        $clients = Client::orderBy('nom')->get();
        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();

        return view('dossiers.edit', compact('dossier', 'clients', 'avocats'));
    }

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

    public function destroy(Dossier $dossier): RedirectResponse
    {
        $dossier->delete();

        return redirect()
            ->route('dossiers.index')
            ->with('success', 'Le dossier a été supprimé.');
    }

    /**
     * Génère un numéro de dossier unique au format DOS-YYYY-XXXXX.
     */
    private function generateNumeroDossier(): string
    {
        $year = now()->year;
        $last = Dossier::whereYear('created_at', $year)->max('id') ?? 0;
        $sequence = str_pad($last + 1, 5, '0', STR_PAD_LEFT);

        return "DOS-{$year}-{$sequence}";
    }
}
