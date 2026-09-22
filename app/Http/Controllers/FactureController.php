<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\UpdateFactureRequest;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use App\Traits\GeneratesSequentialReference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactureController extends Controller
{
    use GeneratesSequentialReference;

    public function index(Request $request): View
    {
        $statut = $request->string('statut')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        $factures = Facture::query()
            ->with(['client', 'dossier'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($search, fn ($q) => $q->recherche($search))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('factures.index', compact('factures', 'statut', 'search'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('nom')->get();

/*         Resoudre N + 1 probleme
 */        $dossiers = Dossier::with('client')->orderBy('numero_dossier')->get();

        return view('factures.create', compact('clients', 'dossiers'));
    }

    public function store(StoreFactureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['numero_facture'] = $this->generateSequentialReference(Facture::class, 'FAC');

        $facture = Facture::create($data);

        if ($facture->loadMissing('dossier')->dossier) {
            $facture->dossier->enregistrerAction(
                "Facture émise : {$facture->numero_facture} de {$facture->montant} €",
                $request->user(),
            );
        }

        return redirect()
            ->route('factures.show', $facture)
            ->with('success', 'La facture a été créée avec succès.');
    }

    public function show(Facture $facture): View
    {
        $facture->load(['client', 'dossier', 'paiements']);
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture): View
    {
        $clients = Client::orderBy('nom')->get();
        $dossiers = Dossier::with('client')->orderBy('numero_dossier')->get();

        return view('factures.edit', compact('facture', 'clients', 'dossiers'));
    }

    public function update(UpdateFactureRequest $request, Facture $facture): RedirectResponse
    {
        $facture->update($request->validated());

|
    }

    public function destroy(Facture $facture): RedirectResponse
    {
        if ($facture->paiements()->exists()) {
            return redirect()
                ->route('factures.show', $facture)
                ->with('error', 'Impossible de supprimer cette facture : des paiements y sont attachés.');
        }

        $facture->delete();

        return redirect()
            ->route('factures.index')
            ->with('success', 'La facture a été supprimée.');
    }
}
