<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function store(StorePaiementRequest $request, Facture $facture): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Données validées via StorePaiementRequest (montant, date_paiement, mode_paiement, etc.)
        // ---------------------------------------------------------------------
        $data = $request->validated();

        // ---------------------------------------------------------------------
        // Insertion du paiement rattaché à la facture ($facture->paiements)
        // ---------------------------------------------------------------------
        $facture->paiements()->create($data);

        // ---------------------------------------------------------------------
        // Méthode métier Facture::synchroniserStatut() :
        // Recalcule la somme payée. Si total payé >= montant facture, bascule le statut en 'Payée'.
        // Sinon, si la date d'échéance est dépassée, bascule en 'En retard'.
        // ---------------------------------------------------------------------
        $facture->synchroniserStatut();

        // ---------------------------------------------------------------------
        // Si la facture est liée à un dossier juridique, consigne l'encaissement dans le journal du dossier
        // ---------------------------------------------------------------------
        if ($facture->loadMissing('dossier')->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement enregistré de {$data['montant']} € sur la facture {$facture->numero_facture}",
                $request->user(),
            );
        }

        // ---------------------------------------------------------------------
        // Redirection vers la page précédente avec confirmation
        // ---------------------------------------------------------------------
        return back()
            ->with('success', 'Le paiement a été enregistré avec succès.');
    }

    public function destroy(Request $request, Facture $facture, Paiement $paiement): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Mémorise le montant du paiement avant de l'effacer pour la journalisation
        // ---------------------------------------------------------------------
        $montant = $paiement->montant;

        // ---------------------------------------------------------------------
        // Suppression de la ligne du paiement
        // ---------------------------------------------------------------------
        $paiement->delete();

        // ---------------------------------------------------------------------
        // Resynchronise le statut de la facture après déduction du paiement annulé
        // ---------------------------------------------------------------------
        $facture->synchroniserStatut();

        // ---------------------------------------------------------------------
        // Consigne l'annulation du paiement dans l'historique du dossier
        // ---------------------------------------------------------------------
        if ($facture->loadMissing('dossier')->dossier) {
            $facture->dossier->enregistrerAction(
                "Paiement de {$montant} € supprimé de la facture {$facture->numero_facture}",
                $request->user(),
            );
        }

        // ---------------------------------------------------------------------
        // Redirection avec message flash
        // ---------------------------------------------------------------------
        return back()
            ->with('success', 'Le paiement a été supprimé.');
    }
}
