<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use App\Models\Historique;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la création d'un dossier refuse un avocat_id sans le rôle Avocat (RG16).
     */
    public function test_dossier_store_rejects_non_avocat_assignee(): void
    {
        $avocat = User::factory()->avocat()->create();
        $assistant = User::factory()->assistantJuridique()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'En cours',
            'client_id' => $client->id,
            'avocat_id' => $assistant->id,
            'date_ouverture' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('avocat_id', 'L\'avocat sélectionné n\'existe pas ou ne possède pas le rôle Avocat.');
        $this->assertDatabaseMissing('dossiers', ['client_id' => $client->id]);
    }

    /**
     * Vérifie que la création d'une audience refuse un avocat_id sans le rôle Avocat (RG26).
     */
    public function test_audience_store_rejects_non_avocat_assignee(): void
    {
        $avocat = User::factory()->avocat()->create();
        $assistant = User::factory()->assistantJuridique()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->post(route('dossiers.audiences.store', $dossier), [
            'date' => now()->addDays(10)->toDateString(),
            'heure' => '09:30',
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
            'statut' => 'Prévue',
            'avocat_id' => $assistant->id,
        ]);

        $response->assertSessionHasErrors('avocat_id', 'L\'avocat sélectionné n\'existe pas ou ne possède pas le rôle Avocat.');
        $this->assertDatabaseCount('audiences', 0);
    }

    /**
     * Vérifie qu'une facture refuse un dossier qui n'appartient pas au client sélectionné (RG38).
     */
    public function test_facture_store_rejects_dossier_belonging_to_another_client(): void
    {
        $avocat = User::factory()->avocat()->create();
        $clientSelect = Client::factory()->particulier()->create();
        $autreClient = Client::factory()->particulier()->create();
        $dossier = Dossier::factory()->enCours()->create([
            'client_id' => $autreClient->id,
            'avocat_id' => $avocat->id,
        ]);

        $response = $this->actingAs($avocat)->post(route('factures.store'), [
            'montant' => 1500.00,
            'date_facture' => now()->toDateString(),
            'statut' => 'Non payée',
            'client_id' => $clientSelect->id,
            'dossier_id' => $dossier->id,
        ]);

        $response->assertSessionHasErrors('dossier_id', 'Le dossier sélectionné n\'existe pas ou n\'appartient pas à ce client.');
        $this->assertDatabaseMissing('factures', ['dossier_id' => $dossier->id]);
    }

    /**
     * Vérifie que la suppression est bloquée même pour un dossier fermé (T13.5).
     */
    public function test_client_destroy_is_blocked_when_a_closed_dossier_exists(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        Dossier::factory()->ferme()->create(['client_id' => $client->id, 'avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->delete(route('clients.destroy', $client));

        $response->assertRedirectToRoute('clients.show', $client);
        $this->assertModelExists($client);
    }

    /**
     * Vérifie que la suppression est bloquée dès qu'un client a une facture (T13.5).
     */
    public function test_client_destroy_is_blocked_when_a_facture_exists(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        Facture::factory()->nonPayee()->create(['client_id' => $client->id]);

        $response = $this->actingAs($avocat)->delete(route('clients.destroy', $client));

        $response->assertRedirectToRoute('clients.show', $client);
        $this->assertModelExists($client);
    }

    /**
     * Vérifie que l'ouverture d'un dossier est tracée dans l'historique (RG45).
     */
    public function test_opening_a_dossier_is_recorded_in_historique(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'En cours',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
            'date_ouverture' => now()->toDateString(),
        ]);

        $dossier = Dossier::where('client_id', $client->id)->firstOrFail();

        $this->assertDatabaseHas('historiques', [
            'dossier_id' => $dossier->id,
            'user_id' => $avocat->id,
            'action' => "Dossier ouvert : {$dossier->numero_dossier}",
        ]);
    }

    /**
     * Vérifie qu'un changement de statut est tracé dans l'historique (RG46).
     */
    public function test_dossier_statut_change_is_recorded_in_historique(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $this->actingAs($avocat)->put(route('dossiers.update', $dossier), [
            'type_affaire' => $dossier->type_affaire,
            'statut' => 'Fermé',
            'client_id' => $dossier->client_id,
            'avocat_id' => $dossier->avocat_id,
            'date_ouverture' => $dossier->date_ouverture->format('Y-m-d'),
            'date_fermeture' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('historiques', [
            'dossier_id' => $dossier->id,
            'user_id' => $avocat->id,
            'action' => 'Statut modifié : En cours → Fermé',
        ]);
    }

    /**
     * Vérifie qu'une mise à jour sans changement de statut n'ajoute pas d'entrée (RG46).
     */
    public function test_dossier_update_without_statut_change_is_not_recorded(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $this->actingAs($avocat)->put(route('dossiers.update', $dossier), [
            'type_affaire' => $dossier->type_affaire,
            'statut' => 'En cours',
            'client_id' => $dossier->client_id,
            'avocat_id' => $dossier->avocat_id,
            'date_ouverture' => $dossier->date_ouverture->format('Y-m-d'),
        ]);

        $this->assertDatabaseCount('historiques', 0);
    }

    /**
     * Vérifie que la planification d'une audience est tracée dans l'historique (RG48).
     */
    public function test_planning_an_audience_is_recorded_in_historique(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $this->actingAs($avocat)->post(route('dossiers.audiences.store', $dossier), [
            'date' => now()->addDays(10)->toDateString(),
            'heure' => '09:30',
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
            'statut' => 'Prévue',
            'avocat_id' => $avocat->id,
        ]);

        $historique = Historique::where('dossier_id', $dossier->id)->firstOrFail();

        $this->assertStringContainsString('Audience planifiée', $historique->action);
        $this->assertStringContainsString('Tribunal de Première Instance de Casablanca', $historique->action);
        $this->assertSame($avocat->id, $historique->user_id);
    }

    /**
     * Vérifie que l'émission d'une facture liée à un dossier est tracée (RG47).
     */
    public function test_issuing_a_facture_is_recorded_in_historique(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();
        $dossier = Dossier::factory()->enCours()->create([
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
        ]);

        $this->actingAs($avocat)->post(route('factures.store'), [
            'montant' => 1500.00,
            'date_facture' => now()->toDateString(),
            'statut' => 'Non payée',
            'client_id' => $client->id,
            'dossier_id' => $dossier->id,
        ]);

        $facture = Facture::where('dossier_id', $dossier->id)->firstOrFail();

        $this->assertDatabaseHas('historiques', [
            'dossier_id' => $dossier->id,
            'user_id' => $avocat->id,
            'action' => "Facture émise : {$facture->numero_facture} de {$facture->montant} €",
        ]);
    }

    /**
     * Vérifie que le téléversement d'un document est tracé dans l'historique (RG48).
     */
    public function test_uploading_a_document_is_recorded_in_historique(): void
    {
        Storage::fake('public');
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $this->actingAs($avocat)->post(route('dossiers.documents.store', $dossier), [
            'fichier' => UploadedFile::fake()->create('contrat.pdf', 100, 'application/pdf'),
            'type' => 'Contrat',
        ]);

        $this->assertDatabaseHas('historiques', [
            'dossier_id' => $dossier->id,
            'user_id' => $avocat->id,
            'action' => 'Document téléversé : contrat.pdf',
        ]);
    }

    /**
     * Vérifie que le paiement d'une facture est tracé dans l'historique (RG48).
     */
    public function test_registered_payment_is_recorded_in_historique(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);

        $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 400.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Virement bancaire',
        ]);

        $historique = Historique::where('dossier_id', $facture->dossier_id)->firstOrFail();

        $this->assertStringContainsString('Paiement enregistré de 400 €', $historique->action);
        $this->assertStringContainsString("facture {$facture->numero_facture}", $historique->action);
        $this->assertSame($avocat->id, $historique->user_id);
    }

    /**
     * Vérifie qu'un dossier fermé exige une date de fermeture à la création (T13.2).
     */
    public function test_dossier_store_requires_date_fermeture_when_ferme(): void
    {
        $avocat = User::factory()->avocat()->create();
        $client = Client::factory()->particulier()->create();

        $response = $this->actingAs($avocat)->post(route('dossiers.store'), [
            'type_affaire' => 'Droit civil',
            'statut' => 'Fermé',
            'client_id' => $client->id,
            'avocat_id' => $avocat->id,
            'date_ouverture' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('date_fermeture', 'La date de fermeture est requise lorsque le dossier est Fermé.');
        $this->assertDatabaseMissing('dossiers', ['client_id' => $client->id]);
    }

    /**
     * Vérifie qu'un dossier ne peut pas être fermé sans date de fermeture (T13.2).
     */
    public function test_dossier_update_requires_date_fermeture_when_ferme(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->put(route('dossiers.update', $dossier), [
            'type_affaire' => $dossier->type_affaire,
            'statut' => 'Fermé',
            'client_id' => $dossier->client_id,
            'avocat_id' => $dossier->avocat_id,
            'date_ouverture' => $dossier->date_ouverture->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('date_fermeture', 'La date de fermeture est requise lorsque le dossier est Fermé.');
        $this->assertSame('En cours', $dossier->fresh()->statut);
    }

    /**
     * Vérifie que le montant d'une facture ne peut pas passer sous le montant déjà payé (T13.3).
     */
    public function test_facture_update_rejects_montant_below_paid_amount(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);
        Paiement::factory()->create(['facture_id' => $facture->id, 'montant' => 400.00]);

        $response = $this->actingAs($avocat)->put(route('factures.update', $facture), [
            'montant' => 200.00,
            'date_facture' => $facture->date_facture->format('Y-m-d'),
            'statut' => 'Non payée',
            'client_id' => $facture->client_id,
            'dossier_id' => $facture->dossier_id,
        ]);

        $response->assertSessionHasErrors('montant', 'Le montant de la facture ne peut pas être inférieur au montant déjà payé.');
        $this->assertSame('1000.00', (string) $facture->fresh()->montant);
    }

    /**
     * Vérifie que la suppression d'une facture est bloquée dès qu'un paiement y est attaché (T13.5).
     */
    public function test_facture_destroy_is_blocked_when_paiement_exists(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);
        $paiement = Paiement::factory()->create(['facture_id' => $facture->id, 'montant' => 400.00]);

        $response = $this->actingAs($avocat)->delete(route('factures.destroy', $facture));

        $response->assertRedirectToRoute('factures.show', $facture);
        $response->assertSessionHas('error', 'Impossible de supprimer cette facture : des paiements y sont attachés.');
        $this->assertModelExists($facture);
        $this->assertModelExists($paiement->fresh());
    }
}
