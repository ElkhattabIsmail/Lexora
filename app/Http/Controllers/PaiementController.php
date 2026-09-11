<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Enregistre un paiement sur une facture et synchronise son statut.
     */
    public function store(StorePaiementRequest $request, Facture $facture): RedirectResponse
    {
        $data = $request->validated();

        $facture->paiements()->create($data);
        $facture->synchroniserStatut();

        if ($facture->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement enregistré de {$data['montant']} € sur la facture {$facture->numero_facture}",
                $request->user(),
            );
        }

        return back()
            ->with('success', 'Le paiement a été enregistré avec succès.');
    }

    /**
     * Supprime un paiement et resynchronise le statut de la facture.
     */
    public function destroy(Request $request, Facture $facture, Paiement $paiement): RedirectResponse
    {
        $montant = $paiement->montant;

        $paiement->delete();
        $facture->synchroniserStatut();

        if ($facture->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement de {$montant} € supprimé de la facture {$facture->numero_facture}",
                $request->user(),
            );
        }

        return back()
            ->with('success', 'Le paiement a été supprimé.');
    }
}
