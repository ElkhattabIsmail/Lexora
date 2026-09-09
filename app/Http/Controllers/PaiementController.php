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
        $facture->paiements()->create($request->validated());

        if ((float) $facture->montant_restant <= 0 && ! $facture->isPayee()) {
            $facture->update(['statut' => 'Payée']);
        }

        if ($facture->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement enregistré de {$request->montant} € sur la facture {$facture->numero_facture}",
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
        $paiement->delete();

        if ($facture->isPayee() && (float) $facture->montant_restant > 0) {
            $facture->update(['statut' => 'Non payée']);
        }

        if ($facture->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement de {$paiement->montant} € supprimé de la facture {$facture->numero_facture}",
                $request->user(),
            );
        }

        return back()
            ->with('success', 'Le paiement a été supprimé.');
    }
}
