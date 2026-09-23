<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DossierCrudTest extends TestCase
{
    // Reset the database between tests
    use RefreshDatabase;

    /**
     * Verify that a lawyer can access the dossier list.
     */
    public function test_avocat_can_view_list_of_dossiers(): void
    {
        // Create a lawyer user
        $avocat = User::factory()->avocat()->create();

        // Create a dossier assigned to this lawyer
        $dossier = Dossier::factory()
            ->enCours()
            ->create(['avocat_id' => $avocat->id]);

        // Simulate a logged-in lawyer sending a GET request
        $response = $this->actingAs($avocat)
            ->get(route('dossiers.index'));

        // Verify that the correct view is returned
        $response->assertViewIs('dossiers.index');

        // Verify that the dossier number is displayed
        $response->assertSee($dossier->numero_dossier);
    }

    /**
     * Verify that the dossier list can be filtered by status.
     */
    public function test_dossier_index_filters_by_statut(): void
    {
        // Create a lawyer
        $avocat = User::factory()->avocat()->create();

        // Create two dossiers with different statuses
        $enCours = Dossier::factory()
            ->enCours()
            ->create(['avocat_id' => $avocat->id]);

        $gagne = Dossier::factory()
            ->gagne()
            ->create(['avocat_id' => $avocat->id]);

        // Send a request with the "Gagné" status filter
        $response = $this->actingAs($avocat)
            ->get(route('dossiers.index', ['statut' => 'Gagné']));

        // The "Gagné" dossier should be displayed
        $response->assertSee($gagne->numero_dossier);

        // The "En cours" dossier should not be displayed
        $response->assertDontSee($enCours->numero_dossier);
    }

    /**
     * Verify that the dossier list can be searched by reference.
     */
    public function test_dossier_index_searches_by_reference(): void
    {
        // Create a lawyer
        $avocat = User::factory()->avocat()->create();

        // Create the dossier that should be found
        $found = Dossier::factory()
            ->enCours()
            ->create(['avocat_id' => $avocat->id]);

        // Create another dossier that should not be displayed
        $hidden = Dossier::factory()
            ->gagne()
            ->create(['avocat_id' => $avocat->id]);

        // Search using the dossier reference number
        $response = $this->actingAs($avocat)
            ->get(route('dossiers.index', [
                'search' => $found->numero_dossier
            ]));

        // Verify that the matching dossier is displayed
        $response->assertSee($found->numero_dossier);

        // Verify that the other dossier is not displayed
        $response->assertDontSee($hidden->numero_dossier);
    }

    /**
     * Verify that the dossier list can be searched by client name.
     */
    public function test_dossier_index_searches_by_client(): void
    {
        // Create a lawyer
        $avocat = User::factory()->avocat()->create();

        // Create the client that should be found
        $foundClient = Client::factory()
            ->particulier()
            ->create([
                'nom' => 'Martin',
                'prenom' => 'Paul'
            ]);

        // Create another client that should not be found
        $hiddenClient = Client::factory()
            ->particulier()
            ->create([
                'nom' => 'Berrada',
                'prenom' => 'Salma'
            ]);

        // Create dossiers for both clients
        $found = Dossier::factory()->enCours()->create([
            'client_id' => $foundClient->id,
            'avocat_id' => $avocat->id
        ]);

        $hidden = Dossier::factory()->enCours()->create([
            'client_id' => $hiddenClient->id,
            'avocat_id' => $avocat->id
        ]);

        // Search for the client using the last name
        $response = $this->actingAs($avocat)
            ->get(route('dossiers.index', ['search' => 'Martin']));

        // The matching client's dossier should be displayed
        $response->assertSee($found->numero_dossier);

        // The other client's dossier should not be displayed
        $response->assertDontSee($hidden->numero_dossier);
    }

    /**
     * Verify that a valid request creates a dossier with a generated number.
     */
    public function test_valid_payload_creates_dossier_and_generates_numero(): void
    {
        // Create a lawyer and a client
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        // Send a POST request to create the dossier
        $response = $this->actingAs($avocat)->post(
            route('dossiers.store'),
            [
                'type_affaire' => 'Droit civil',
                'statut' => 'En cours',
                'client_id' => $client->id,
                'avocat_id' => $avocat->id,
                'date_ouverture' => now()->toDateString(),
            ]
        );

        // Define the expected generated dossier number
        $numero = 'DOS-'.now()->year.'-00001';

        // Find the newly created dossier
        $dossier = Dossier::where('numero_dossier', $numero)
            ->firstOrFail();

        // Verify that the user is redirected to the dossier details page
        $response->assertRedirectToRoute('dossiers.show', $dossier);

        // Verify that the dossier was correctly saved in the database
        $this->assertDatabaseHas('dossiers', [
            'numero_dossier' => $numero,
            'statut' => 'En cours',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);
    }

    /**
     * Verify that an invalid dossier status is rejected.
     */
    public function test_dossier_store_rejects_unknown_statut(): void
    {
        // Create a lawyer and a client
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        // Try to create a dossier with an invalid status
        $response = $this->actingAs($avocat)->post(
            route('dossiers.store'),
            [
                'type_affaire' => 'Droit civil',
                'statut' => 'Ouvert',
                'client_id' => $client->id,
                'avocat_id' => $avocat->id,
                'date_ouverture' => now()->toDateString(),
            ]
        );

        // Verify that a validation error is returned for the status
        $response->assertSessionHasErrors(
            'statut',
            'Le statut doit être : En cours, Gagné, Perdu ou Fermé.'
        );

        // Verify that no dossier was created
        $this->assertDatabaseMissing('dossiers', [
            'type_affaire' => 'Droit civil'
        ]);
    }

    /**
     * Verify that client and lawyer are required when creating a dossier.
     */
    public function test_dossier_store_requires_client_and_avocat(): void
    {
        // Create a lawyer
        $avocat = User::factory()->avocat()->create();

        // Send a request without client_id and avocat_id
        $response = $this->actingAs($avocat)->post(
            route('dossiers.store'),
            [
                'type_affaire' => 'Droit civil',
                'statut' => 'En cours',
                'date_ouverture' => now()->toDateString(),
            ]
        );

        // Verify that validation errors exist for both fields
        $response->assertSessionHasErrors([
            'client_id',
            'avocat_id'
        ]);
    }

    /**
     * Verify that the dossier details page displays the dossier information.
     */
    public function test_dossier_show_displays_dossier_details(): void
    {
        // Create a lawyer, a client, and a dossier
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $dossier = Dossier::factory()->enCours()->create([
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);

        // Open the dossier details page
        $response = $this->actingAs($avocat)
            ->get(route('dossiers.show', $dossier));

        // Verify that the correct view is returned
        $response->assertViewIs('dossiers.show');

        // Verify that the dossier number is displayed
        $response->assertSee($dossier->numero_dossier);

        // Verify that the client name is displayed
        $response->assertSee($client->nom_complet);
    }

    /**
     * Verify that a valid request updates the dossier status.
     */
    public function test_valid_payload_updates_dossier(): void
    {
        // Create a lawyer and an existing dossier
        $avocat = User::factory()->avocat()->create();

        $dossier = Dossier::factory()
            ->enCours()
            ->create(['avocat_id' => $avocat->id]);

        // Send a PUT request to update the dossier
        $response = $this->actingAs($avocat)->put(
            route('dossiers.update', $dossier),
            [
                'type_affaire' => $dossier->type_affaire,
                'statut' => 'Fermé',
                'client_id' => $dossier->client_id,
                'avocat_id' => $dossier->avocat_id,
                'date_ouverture' => $dossier->date_ouverture->format('Y-m-d'),
                'date_fermeture' => now()->toDateString(),
            ]
        );

        // Verify that the user is redirected to the dossier details page
        $response->assertRedirectToRoute('dossiers.show', $dossier);

        // Reload the dossier from the database
        $this->assertSame(
            'Fermé',
            $dossier->fresh()->statut
        );

        // Verify that the database contains the updated status
        $this->assertDatabaseHas('dossiers', [
            'numero_dossier' => $dossier->numero_dossier,
            'statut' => 'Fermé',
        ]);
    }

    /**
     * Verify that a dossier can be deleted.
     */
    public function test_dossier_destroy_removes_dossier(): void
    {
        // Create a lawyer and a dossier
        $avocat = User::factory()->avocat()->create();

        $dossier = Dossier::factory()
            ->enCours()
            ->create(['avocat_id' => $avocat->id]);

        // Send a DELETE request to remove the dossier
        $response = $this->actingAs($avocat)
            ->delete(route('dossiers.destroy', $dossier));

        // Verify that the user is redirected to the dossier list
        $response->assertRedirectToRoute('dossiers.index');

        // Verify that the dossier was removed from the database
        $this->assertDatabaseMissing('dossiers', [
            'id' => $dossier->id
        ]);
    }
}

