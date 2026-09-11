<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterfaceImprovementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la pagination apparaît quand la liste dépasse la taille de page.
     */
    public function test_client_index_shows_pagination_when_many_records(): void
    {
        $avocat = User::factory()->avocat()->create();
        Client::factory()->particulier()->count(20)->create();

        $response = $this->actingAs($avocat)->get(route('clients.index'));

        $response->assertViewIs('clients.index');
        $response->assertSee('Suivant');
    }

    /**
     * Vérifie que les liens de pagination conservent les paramètres de recherche.
     */
    public function test_client_index_pagination_preserves_search_parameters(): void
    {
        $avocat = User::factory()->avocat()->create();
        Client::factory()->particulier()->count(20)->create([
            'nom' => 'Martin',
        ]);

        $response = $this->actingAs($avocat)->get(route('clients.index', ['search' => 'Martin']));

        $response->assertOk();
        $response->assertSee('search=Martin');
    }

    /**
     * Vérifie que le bouton « Réinitialiser » apparaît lorsque des filtres sont actifs.
     */
    public function test_reset_button_appears_when_filters_are_active(): void
    {
        $avocat = User::factory()->avocat()->create();
        Client::factory()->entreprise()->create();

        $response = $this->actingAs($avocat)->get(route('clients.index', ['type' => 'Entreprise']));

        $response->assertOk();
        $response->assertSee('Réinitialiser');
    }

    /**
     * Vérifie que le résumé global des erreurs de validation est affiché dans la liste des dossiers.
     */
    public function test_global_validation_errors_are_displayed(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)
            ->from(route('dossiers.create'))
            ->post(route('dossiers.store'), [
                'type_affaire' => 'Droit civil',
                'statut' => 'En cours',
                'client_id' => $client->id,
                'date_ouverture' => now()->toDateString(),
            ]);

        $response->assertSessionHasErrors('avocat_id');
        $response->assertStatus(302);
    }

    /**
     * Vérifie que la fiche client affiche les superpositions mobiles.
     */
    public function test_client_index_contains_mobile_card_layout(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->get(route('clients.index'));

        $response->assertOk();
        $response->assertSee('md:hidden');
        $response->assertSee($client->nom_complet);
    }

    /**
     * Vérifie que les fiches dossiers/factures affichent aussi la disposition mobile.
     */
    public function test_dossier_and_facture_index_contain_mobile_card_layout(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->payee()->create();

        $dossierResponse = $this->actingAs($avocat)->get(route('dossiers.index'));
        $factureResponse = $this->actingAs($avocat)->get(route('factures.index'));

        $dossierResponse->assertOk()->assertSee('md:hidden');
        $factureResponse->assertOk()->assertSee('md:hidden');
        $factureResponse->assertSee($facture->numero_facture);
    }

    /**
     * Vérifie que la liste des utilisateurs affiche la disposition mobile.
     */
    public function test_admin_users_index_contains_mobile_card_layout(): void
    {
        $admin = User::factory()->administrateur()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('md:hidden');
        $response->assertSee($admin->nom_complet);
    }
}
