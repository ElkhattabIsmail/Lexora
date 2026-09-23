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
    // -------------------------------------------------------------------------
    // Trait fournissant la méthode generateSequentialReference($model, $prefix)
    // Permet de générer des références uniques au format FAC-AAAA-XXXXX
    // -------------------------------------------------------------------------
    use GeneratesSequentialReference;

    public function index(Request $request): View
    {
        // ---------------------------------------------------------------------
        // Nettoyage et typage des paramètres de filtrage passés dans l'URL
        // ---------------------------------------------------------------------
        $statut = $request->string('statut')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        // ---------------------------------------------------------------------
        // Construction de la requête pour afficher la liste paginée des factures :
        // ---------------------------------------------------------------------
        $factures = Facture::query()
            // -----------------------------------------------------------------
            // with(['client', 'dossier']) : Eager loading pour éviter le problème N+1
            // -----------------------------------------------------------------
            ->with(['client', 'dossier'])
            // -----------------------------------------------------------------
            // when($condition, ...) : Filtre conditionnel sur le statut ('En attente', 'Payée', 'En retard', etc.)
            // -----------------------------------------------------------------
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            // -----------------------------------------------------------------
            // scopeRecherche($search) : Scope de recherche multicritère (numéro, client, dossier)
            // -----------------------------------------------------------------
            ->when($search, fn ($q) => $q->recherche($search))
            // -----------------------------------------------------------------
            // latest() : Trie les factures de la plus récente à la plus ancienne
            // -----------------------------------------------------------------
            ->latest()
            // -----------------------------------------------------------------
            // paginate(15) : Pagination par tranche de 15 éléments
            // -----------------------------------------------------------------
            ->paginate(15)
            // -----------------------------------------------------------------
            // withQueryString() : Conserve les filtres de recherche lors des changements de page
            // -----------------------------------------------------------------
            ->withQueryString();

        // ---------------------------------------------------------------------
        // Retourne la vue 'resources/views/factures/index.blade.php'
        // ---------------------------------------------------------------------
        return view('factures.index', compact('factures', 'statut', 'search'));
    }

    public function create(): View
    {
        // ---------------------------------------------------------------------
        // Liste triée des clients pour le champ de sélection du client
        // ---------------------------------------------------------------------
        $clients = Client::orderBy('nom')->get();

        // ---------------------------------------------------------------------
        // Eager loading de 'client' sur les dossiers pour afficher "DOS-XXXX (Client Nom)" sans requêtes N+1
        // ---------------------------------------------------------------------
        $dossiers = Dossier::with('client')->orderBy('numero_dossier')->get();

        // ---------------------------------------------------------------------
        // Affiche la vue du formulaire de facturation
        // ---------------------------------------------------------------------
        return view('factures.create', compact('clients', 'dossiers'));
    }

    public function store(StoreFactureRequest $request): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Extraction des données validées (montant, date_emission, client_id, etc.)
        // ---------------------------------------------------------------------
        $data = $request->validated();

        // ---------------------------------------------------------------------
        // Génération automatique du numéro séquentiel unique (ex: FAC-2026-00001)
        // ---------------------------------------------------------------------
        $data['numero_facture'] = $this->generateSequentialReference(Facture::class, 'FAC');

        // ---------------------------------------------------------------------
        // Insertion de la facture en base de données
        // ---------------------------------------------------------------------
        $facture = Facture::create($data);

        // ---------------------------------------------------------------------
        // loadMissing('dossier') : Si un dossier est rattaché à cette facture,
        // on charge la relation et on enregistre l'action d'émission dans l'historique du dossier
        // ---------------------------------------------------------------------
        if ($facture->loadMissing('dossier')->dossier) {
            $facture->dossier->enregistrerAction(
                "Facture émise : {$facture->numero_facture} de {$facture->montant} €",
                $request->user(),
            );
        }

        // ---------------------------------------------------------------------
        // Redirection vers le détail de la facture avec notification flash
        // ---------------------------------------------------------------------
        return redirect()
            ->route('factures.show', $facture)
            ->with('success', 'La facture a été créée avec succès.');
    }

    public function show(Facture $facture): View
    {
        // ---------------------------------------------------------------------
        // load(...) : Charge en une seule passe le client, le dossier et tous les paiements perçus
        // ---------------------------------------------------------------------
        $facture->load(['client', 'dossier', 'paiements']);

        // ---------------------------------------------------------------------
        // Affiche la vue de détail avec récapitulatif des règlements
        // ---------------------------------------------------------------------
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture): View
    {
        // ---------------------------------------------------------------------
        // Chargement des listes de clients et dossiers pour le formulaire d'édition
        // ---------------------------------------------------------------------
        $clients = Client::orderBy('nom')->get();
        $dossiers = Dossier::with('client')->orderBy('numero_dossier')->get();

        // ---------------------------------------------------------------------
        // Affiche le formulaire d'édition prérempli
        // ---------------------------------------------------------------------
        return view('factures.edit', compact('facture', 'clients', 'dossiers'));
    }

    public function update(UpdateFactureRequest $request, Facture $facture): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Application des modifications validées (mise à jour montant, statut, etc.)
        // ---------------------------------------------------------------------
        $facture->update($request->validated());

        // ---------------------------------------------------------------------
        // Redirection vers la facture avec message flash de confirmation
        // ---------------------------------------------------------------------
        return redirect()
            ->route('factures.show', $facture)
            ->with('success', 'La facture a été mise à jour avec succès.');
    }

    public function destroy(Facture $facture): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Règle de sécurité comptable :
        // Interdiction formelle de supprimer une facture si des paiements y sont déjà rattachés.
        // exists() effectue une vérification SQL immédiate sans charger la collection.
        // ---------------------------------------------------------------------
        if ($facture->paiements()->exists()) {
            return redirect()
                ->route('factures.show', $facture)
                ->with('error', 'Impossible de supprimer cette facture : des paiements y sont attachés.');
        }

        // ---------------------------------------------------------------------
        // Suppression de la facture non encaissée
        // ---------------------------------------------------------------------
        $facture->delete();

        // ---------------------------------------------------------------------
        // Redirection vers la liste des factures
        // ---------------------------------------------------------------------
        return redirect()
            ->route('factures.index')
            ->with('success', 'La facture a été supprimée.');
    }
}
