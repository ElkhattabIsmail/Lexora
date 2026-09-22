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
 *
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
    /**
     * Displays the paginated list of clients with optional search and type filters.
     *
     * Query string parameters:
     *   @param  string  $search  (optional) Free-text search across nom, prenom, email.
     *   @param  string  $type    (optional) Filter by client type: "Particulier" | "Entreprise".
     *
     * @return View  clients.index  with: $clients (paginated with dossiers count), $search, $type
     *
     * Similar: DossierController::index(), FactureController::index() — same filter+paginate pattern.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $type = $request->string('type')->trim()->toString();

        $clients = Client::query()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->recherche($search))
            ->withCount('dossiers')
            ->latest()
            ->paginate(15)
            ->withQueryString();     

        return view('clients.index', compact('clients', 'search', 'type'));
    }

    /**
     * Shows the blank client creation form.
     *
     * @return View  clients.create
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Validates the form data and creates a new Client record.
     *
     * @param  StoreClientRequest  $request  Validated form data.
     *
     * @return RedirectResponse  Redirects to clients.show on success.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = Client::create($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Le client a été créé avec succès.');
    }

    /**
     * Displays a single client with their related dossiers and factures.
     *
     * @param  Client  $client  Route-model-bound client instance.
     *
     * Loaded relations: dossiers.avocat, factures
     *
     * @return View  clients.show  with: $client
     */
    public function show(Client $client): View
    {
        $client->load(['dossiers.avocat', 'factures']);

        return view('clients.show', compact('client'));
    }

    /**
     * Shows the edit form for an existing client.
     *
     * @param  Client  $client  Route-model-bound client instance.
     *
     * @return View  clients.edit  with: $client
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Validates and saves changes to an existing client.
     *
     * @param  UpdateClientRequest  $request  Validated update payload.
     * @param  Client               $client   Route-model-bound client to update.
     *
     * @return RedirectResponse  Redirects to clients.show on success.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Les informations du client ont été mises à jour.');
    }

    /**
     * Deletes a client if they have no associated dossiers or factures.
     * Guards against orphaned financial/legal data by refusing deletion
     * when related records exist.
     *
     * @param  Client  $client  Route-model-bound client to delete.
     *
     * @return RedirectResponse  Redirects to clients.show with an error if blocked,
     *                           or to clients.index on success.
     */
    public function destroy(Client $client): RedirectResponse
    {
        if ($client->dossiers()->exists() || $client->factures()->exists()) { 
            return redirect()
                ->route('clients.show', $client)
                ->with('error', 'Impossible de supprimer ce client : il possède des dossiers ou des factures.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Le client a été supprimé.');
    }
}
