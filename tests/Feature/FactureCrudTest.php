<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'un avocat accède à la liste des factures.
     */
    public function test_avocat_can_view_list_of_factures(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->get(route('factures.index'));

        $response->assertViewIs('factures.index');
        $response->assertSee($facture->numero_facture);
    }

    /**
     * Vérifie que le filtre par statut ne renvoie que les factures correspondantes.
     */
    public function test_facture_index_filters_by_statut(): void
    {
        $avocat = User::factory()->avocat()->create();
        $payee = Facture::factory()->payee()->create();
        $nonPayee = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->get(route('factures.index', ['statut' => 'Payée']));

        $response->assertSee($payee->numero_facture);
        $response->assertDontSee($nonPayee->numero_facture);
    }

    /**
     * Vérifie que la recherche par référence ne renvoie que la facture correspondante.
     */
    public function test_facture_index_searches_by_reference(): void
    {
        $avocat = User::factory()->avocat()->create();
        $found = Facture::factory()->payee()->create();
        $hidden = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->get(route('factures.index', ['search' => $found->numero_facture]));

        $response->assertSee($found->numero_facture);
        $response->assertDontSee($hidden->numero_facture);
    }

    /**
     * Vérifie que la recherche par client ne renvoie que les factures correspondantes.
     */
    public function test_facture_index_searches_by_client(): void
    {
        $avocat = User::factory()->avocat()->create();
        $foundClient = Client::factory()->particulier()->create(['nom' => 'Martin', 'prenom' => 'Paul']);
        $hiddenClient = Client::factory()->particulier()->create(['nom' => 'Berrada', 'prenom' => 'Salma']);
        $found = Facture::factory()->payee()->create(['client_id' => $foundClient->id]);
        $hidden = Facture::factory()->nonPayee()->create(['client_id' => $hiddenClient->id]);

        $response = $this->actingAs($avocat)->get(route('factures.index', ['search' => 'Martin']));

        $response->assertSee($found->numero_facture);
        $response->assertDontSee($hidden->numero_facture);
    }

    /**
     * Vérifie qu'une requête valide crée la facture avec un numéro généré.
     */
    public function test_valid_payload_creates_facture_and_generates_numero(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        $dossier = Dossier::factory()->enCours()->create([
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);

        $response = $this->actingAs($avocat)->post(route('factures.store'), [
            'montant' => 1500.00,
            'date_facture' => now()->toDateString(),
            'statut' => 'Non payée',
            'client_id' => $client->id,
            'dossier_id' => $dossier->id,
        ]);

        $numero = 'FAC-'.now()->year.'-00001';
        $facture = Facture::where('numero_facture', $numero)->firstOrFail();

        $response->assertRedirectToRoute('factures.show', $facture);
        $this->assertDatabaseHas('factures', [
            'numero_facture' => $numero,
            'statut' => 'Non payée',
            'client_id' => $client->id,
            'dossier_id' => $dossier->id,
        ]);
        $this->assertSame('1500.00', (string) $facture->montant);
    }

    /**
     * Vérifie que la création refuse un montant négatif.
     */
    public function test_facture_store_rejects_negative_montant(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->post(route('factures.store'), [
            'montant' => -150,
            'date_facture' => now()->toDateString(),
            'statut' => 'Payée',
            'client_id' => $client->id,
        ]);

        $response->assertSessionHasErrors('montant', 'Le montant ne peut pas être négatif.');
        $this->assertDatabaseMissing('factures', ['client_id' => $client->id]);
    }

    /**
     * Vérifie que la création exige montant, date, statut et client.
     */
    public function test_facture_store_requires_montant_date_statut_and_client(): void
    {
        $avocat = User::factory()->avocat()->create();

        $response = $this->actingAs($avocat)->post(route('factures.store'), []);

        $response->assertSessionHasErrors(['montant', 'date_facture', 'statut', 'client_id']);
    }

    /**
     * Vérifie que la fiche facture affiche le détail et les paiements.
     */
    public function test_facture_show_displays_facture_with_paiements(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->payee()->create();
        $paiement = Paiement::factory()->create([
            'facture_id' => $facture->id,
            'montant' => 1500.00,
            'mode_paiement' => 'Virement bancaire',
            'reference' => 'REF-ABC-123',
        ]);

        $response = $this->actingAs($avocat)->get(route('factures.show', $facture));

        $response->assertViewIs('factures.show');
        $response->assertSee($facture->numero_facture);
        $response->assertSee('Virement bancaire');
        $response->assertSee('REF-ABC-123');
    }

    /**
     * Vérifie qu'une requête valide met à jour la facture.
     */
    public function test_valid_payload_updates_facture(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->put(route('factures.update', $facture), [
            'montant' => 2500.00,
            'date_facture' => $facture->date_facture->format('Y-m-d'),
            'statut' => 'Payée',
            'client_id' => $facture->client_id,
            'dossier_id' => $facture->dossier_id,
        ]);

        $response->assertRedirectToRoute('factures.show', $facture);
        $this->assertSame('Payée', $facture->fresh()->statut);
        $this->assertDatabaseHas('factures', [
            'numero_facture' => $facture->numero_facture,
            'statut' => 'Payée',
            'montant' => '2500.00',
        ]);
    }

    /**
     * Vérifie qu'une facture peut être supprimée.
     */
    public function test_facture_destroy_removes_facture(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->delete(route('factures.destroy', $facture));

        $response->assertRedirectToRoute('factures.index');
        $this->assertDatabaseMissing('factures', ['id' => $facture->id]);
    }
}
