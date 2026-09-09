<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DossierCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'un avocat accède à la liste des dossiers.
     */
    public function test_avocat_can_view_list_of_dossiers(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->get(route('dossiers.index'));

        $response->assertViewIs('dossiers.index');
        $response->assertSee($dossier->numero_dossier);
    }

    /**
     * Vérifie que le filtre par statut ne renvoie que les dossiers correspondants.
     */
    public function test_dossier_index_filters_by_statut(): void
    {
        $avocat = User::factory()->avocat()->create();
        $enCours = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        $gagne = Dossier::factory()->gagne()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->get(route('dossiers.index', ['statut' => 'Gagné']));

        $response->assertSee($gagne->numero_dossier);
        $response->assertDontSee($enCours->numero_dossier);
    }

    /**
     * Vérifie qu'une requête valide crée le dossier avec un numéro généré.
     */
    public function test_valid_payload_creates_dossier_and_generates_numero(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'En cours',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
            'date_ouverture' => now()->toDateString(),
        ]);

        $numero = 'DOS-'.now()->year.'-00001';
        $dossier = Dossier::where('numero_dossier', $numero)->firstOrFail();

        $response->assertRedirectToRoute('dossiers.show', $dossier);
        $this->assertDatabaseHas('dossiers', [
            'numero_dossier' => $numero,
            'statut' => 'En cours',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);
    }

    /**
     * Vérifie que la création refuse un statut de dossier inconnu.
     */
    public function test_dossier_store_rejects_unknown_statut(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'Ouvert',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
            'date_ouverture' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('statut', 'Le statut doit être : En cours, Gagné, Perdu ou Fermé.');
        $this->assertDatabaseMissing('dossiers', ['type_affaire' => 'Droit civil']);
    }

    /**
     * Vérifie que la création exige un client et un avocat.
     */
    public function test_dossier_store_requires_client_and_avocat(): void
    {
        $avocat = User::factory()->avocat()->create();

        $response = $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'En cours',
            'date_ouverture' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['client_id', 'avocat_id']);
    }

    /**
     * Vérifie que la fiche dossier affiche les informations du dossier.
     */
    public function test_dossier_show_displays_dossier_details(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        $dossier = Dossier::factory()->enCours()->create([
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);

        $response = $this->actingAs($avocat)->get(route('dossiers.show', $dossier));

        $response->assertViewIs('dossiers.show');
        $response->assertSee($dossier->numero_dossier);
        $response->assertSee($client->nom_complet);
    }

    /**
     * Vérifie qu'une requête valide met à jour le statut du dossier.
     */
    public function test_valid_payload_updates_dossier(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->put(route('dossiers.update', $dossier), [
            'type_affaire' => $dossier->type_affaire,
            'statut' => 'Fermé',
            'client_id' => $dossier->client_id,
            'avocat_id' => $dossier->avocat_id,
            'date_ouverture' => $dossier->date_ouverture->format('Y-m-d'),
            'date_fermeture' => now()->toDateString(),
        ]);

        $response->assertRedirectToRoute('dossiers.show', $dossier);
        $this->assertSame('Fermé', $dossier->fresh()->statut);
        $this->assertDatabaseHas('dossiers', [
            'numero_dossier' => $dossier->numero_dossier,
            'statut' => 'Fermé',
        ]);
    }

    /**
     * Vérifie qu'un dossier peut être supprimé.
     */
    public function test_dossier_destroy_removes_dossier(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->delete(route('dossiers.destroy', $dossier));

        $response->assertRedirectToRoute('dossiers.index');
        $this->assertDatabaseMissing('dossiers', ['id' => $dossier->id]);
    }
}
