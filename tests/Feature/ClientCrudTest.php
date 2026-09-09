<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que les invités sont redirigés vers la page de connexion.
     */
    public function test_guest_is_redirected_to_login_from_client_routes(): void
    {
        $this->get(route('clients.index'))->assertRedirect('/login');
    }

    /**
     * Vérifie qu'un assistant juridique reçoit une erreur 403 sur les routes clients.
     */
    public function test_assistant_juridique_cannot_access_client_routes(): void
    {
        $assistant = User::factory()->assistantJuridique()->create();

        $this->actingAs($assistant)->get(route('clients.index'))->assertForbidden();
    }

    /**
     * Vérifie qu'un avocat accède à la liste des clients.
     */
    public function test_avocat_can_view_list_of_clients(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->get(route('clients.index'));

        $response->assertViewIs('clients.index');
        $response->assertSee($client->nom_complet);
        $response->assertSee($client->email);
    }

    /**
     * Vérifie que la recherche filtre les clients par nom.
     */
    public function test_client_index_filters_by_search(): void
    {
        $avocat = User::factory()->avocat()->create();
        $found = Client::factory()->particulier()->create(['nom' => 'Martin', 'prenom' => 'Paul']);
        $hidden = Client::factory()->particulier()->create(['nom' => 'Berrada', 'prenom' => 'Salma']);

        $response = $this->actingAs($avocat)->get(route('clients.index', ['search' => 'Martin']));

        $response->assertSee($found->nom_complet);
        $response->assertDontSee($hidden->nom_complet);
    }

    /**
     * Vérifie que le filtre par type ne renvoie que les clients correspondants.
     */
    public function test_client_index_filters_by_type(): void
    {
        $avocat = User::factory()->avocat()->create();
        $entreprise = Client::factory()->entreprise()->create(['nom' => 'Acme SARL']);
        $particulier = Client::factory()->particulier()->create(['nom' => 'Berrada', 'prenom' => 'Salma']);

        $response = $this->actingAs($avocat)->get(route('clients.index', ['type' => 'Entreprise']));

        $response->assertSee($entreprise->nom);
        $response->assertDontSee($particulier->nom_complet);
    }

    /**
     * Vérifie qu'une requête valide crée le client et redirige vers sa fiche.
     */
    public function test_valid_payload_creates_client_and_redirects_to_show(): void
    {
        $avocat = User::factory()->avocat()->create();

        $response = $this->actingAs($avocat)->post(route('clients.store'), [
            'type' => 'Particulier',
            'nom' => 'Martin',
            'prenom' => 'Paul',
            'email' => 'paul.martin@example.fr',
            'telephone' => '0600000001',
            'adresse' => '10 rue de la Paix, Paris',
        ]);

        $client = Client::where('email', 'paul.martin@example.fr')->firstOrFail();

        $response->assertRedirectToRoute('clients.show', $client);
        $this->assertModelExists($client);
        $this->assertSame('Paul Martin', $client->nom_complet);
    }

    /**
     * Vérifie que la création refuse un type de client inconnu.
     */
    public function test_client_store_rejects_unknown_type(): void
    {
        $avocat = User::factory()->avocat()->create();

        $response = $this->actingAs($avocat)->post(route('clients.store'), [
            'type' => 'Société',
            'nom' => 'Test',
        ]);

        $response->assertSessionHasErrors('type', 'Le type doit être Particulier ou Entreprise.');
        $this->assertDatabaseMissing('clients', ['nom' => 'Test']);
    }

    /**
     * Vérifie que la création exige le type et le nom.
     */
    public function test_client_store_requires_type_and_nom(): void
    {
        $avocat = User::factory()->avocat()->create();

        $response = $this->actingAs($avocat)->post(route('clients.store'), []);

        $response->assertSessionHasErrors(['type', 'nom']);
    }

    /**
     * Vérifie qu'une requête valide met à jour le client.
     */
    public function test_valid_payload_updates_client(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->put(route('clients.update', $client), [
            'type' => 'Entreprise',
            'nom' => 'Cabinet Martin SARL',
            'email' => $client->email,
        ]);

        $response->assertRedirectToRoute('clients.show', $client);
        $this->assertSame('Cabinet Martin SARL', $client->fresh()->nom);
        $this->assertSame('Entreprise', $client->fresh()->type);
    }

    /**
     * Vérifie que la mise à jour refuse une adresse email déjà utilisée.
     */
    public function test_client_update_rejects_duplicate_email(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        $other = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->put(route('clients.update', $client), [
            'type' => $client->type,
            'nom' => $client->nom,
            'email' => $other->email,
        ]);

        $response->assertSessionHasErrors('email', 'Cette adresse email est déjà utilisée par un autre client.');
        $this->assertNotSame($other->email, $client->fresh()->email);
    }

    /**
     * Vérifie qu'un client sans dossier actif peut être supprimé.
     */
    public function test_client_destroy_removes_client_without_active_dossiers(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->delete(route('clients.destroy', $client));

        $response->assertRedirectToRoute('clients.index');
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    /**
     * Vérifie que la suppression est bloquée tant que le client a un dossier actif.
     */
    public function test_client_destroy_is_blocked_when_an_active_dossier_exists(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        Dossier::factory()->enCours()->create(['client_id' => $client->id, 'avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->delete(route('clients.destroy', $client));

        $response->assertRedirectToRoute('clients.show', $client);
        $this->assertModelExists($client);
    }

    /**
     * Vérifie que le contenu saisi par l'utilisateur est échappé dans la fiche client.
     */
    public function test_client_show_escapes_user_provided_content(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create([
            'nom' => "O'Cormac <script>alert('xss')</script>",
        ]);

        $response = $this->actingAs($avocat)->get(route('clients.show', $client));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;', false);
        $response->assertDontSee("<script>alert('xss')</script>", false);
    }
}
