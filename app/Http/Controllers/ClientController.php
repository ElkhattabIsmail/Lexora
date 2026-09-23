<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ClientController — manages CRUD operations for law firm clients.

 * Routes (protected by auth + verified + role:Avocat,Administrateur):
 *   GET    /clients              → index()
 *   GET    /clients/create       → create()
 *   POST   /clients              → store()
 *   GET    /clients/{client}     → show()
 *   GET    /clients/{id}/edit    → edit()
 *   PATCH  /clients/{id}         → update()
 *   DELETE /clients/{id}         → destroy()
 */
class ClientController extends Controller
{
    public function index(Request $request): View
    {
        // ---------------------------------------------------------------------
        // Récupération des filtres depuis la requête GET :
        // ---------------------------------------------------------------------
        $search = $request->string('search')->trim()->toString();
        $type = $request->string('type')->trim()->toString();

        // ---------------------------------------------------------------------
        // Construction dynamique de la requête Eloquent pour lister les clients :
        // ---------------------------------------------------------------------
        $clients = Client::query()
            // -----------------------------------------------------------------
            // when($type, ...) : Filtre sur le type de client ('Particulier' ou 'Entreprise') si renseigné.
            // -----------------------------------------------------------------
            ->when($type, fn ($q) => $q->where('type', $type))
            // -----------------------------------------------------------------
            // scopeRecherche($search) : Recherche textuelle insensible à la casse sur nom, prénom, email, téléphone.
            // -----------------------------------------------------------------
            ->when($search, fn ($q) => $q->recherche($search))
            // -----------------------------------------------------------------
            // withCount('dossiers') :
            // Ajoute une sous-requête SQL "SELECT count(*) FROM dossiers WHERE client_id = clients.id"
            // permettant d'accéder à $client->dossiers_count sans charger les dossiers en mémoire.
            // -----------------------------------------------------------------
            ->withCount('dossiers')
            // -----------------------------------------------------------------
            // latest() : Trie par date de création la plus récente (created_at DESC).
            // -----------------------------------------------------------------
            ->latest()
            // -----------------------------------------------------------------
            // paginate(15) : Découpe les clients en pages de 15 éléments.
            // -----------------------------------------------------------------
            ->paginate(15)
            // -----------------------------------------------------------------
            // withQueryString() : Conserve les filtres 'search' et 'type' dans les URLs de pagination.
            // -----------------------------------------------------------------
            ->withQueryString();

        // ---------------------------------------------------------------------
        // Retourne la vue Blade 'resources/views/clients/index.blade.php'
        // ---------------------------------------------------------------------
        return view('clients.index', compact('clients', 'search', 'type'));
    }

    public function create(): View
    {
        // ---------------------------------------------------------------------
        // Affiche le formulaire vierge de création d'un client.
        // ---------------------------------------------------------------------
        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Client::create(...) :
        // Valide les données via StoreClientRequest et enregistre le nouveau client en base.
        // ---------------------------------------------------------------------
        $client = Client::create($request->validated());

        // ---------------------------------------------------------------------
        // Redirige vers la fiche détaillée du client avec message flash de succès.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Le client a été créé avec succès.');
    }

    public function show(Client $client): View
    {
        // ---------------------------------------------------------------------
        // load(...) :
        // Lazy Eager Loading pour charger les dossiers (avec leur avocat assigné) et les factures associées.
        // Évite le problème de requêtes N+1 lors de l'affichage des onglets.
        // ---------------------------------------------------------------------
        $client->load(['dossiers.avocat', 'factures']);

        // ---------------------------------------------------------------------
        // Affiche la vue 'resources/views/clients/show.blade.php'.
        // ---------------------------------------------------------------------
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        // ---------------------------------------------------------------------
        // Affiche le formulaire d'édition prérempli avec les informations du client.
        // ---------------------------------------------------------------------
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Met à jour les colonnes du client avec les données validées.
        // ---------------------------------------------------------------------
        $client->update($request->validated());

        // ---------------------------------------------------------------------
        // Redirige vers la fiche client avec un message flash de confirmation.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Les informations du client ont été mises à jour.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Garde-fou d'intégrité référentielle :
        // Vérifie via exists() si le client a des dossiers ou des factures en cours.
        // exists() effectue un "SELECT 1" rapide sans charger tous les objets.
        // ---------------------------------------------------------------------
        if ($client->dossiers()->exists() || $client->factures()->exists()) {
            return redirect()
                ->route('clients.show', $client)
                ->with('error', 'Impossible de supprimer ce client : il possède des dossiers ou des factures.');
        }

        // ---------------------------------------------------------------------
        // Suppression sécurisée du client orphelin de tout dossier/facture.
        // ---------------------------------------------------------------------
        $client->delete();

        // ---------------------------------------------------------------------
        // Redirection vers l'index des clients avec message de succès.
        // ---------------------------------------------------------------------
        return redirect()
            ->route('clients.index')
            ->with('success', 'Le client a été supprimé.');
    }
}
