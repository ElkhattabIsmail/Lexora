<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que les invités sont redirigés vers la page de connexion.
     */
    public function test_guest_is_redirected_to_login_from_paiement_routes(): void
    {
        $facture = Facture::factory()->nonPayee()->create();

        $this->post(route('factures.paiements.store', $facture))
            ->assertRedirect('/login');
    }

    /**
     * Vérifie qu'un assistant juridique reçoit une erreur 403 sur les routes paiements.
     */
    public function test_assistant_juridique_cannot_access_paiement_routes(): void
    {
        $assistant = User::factory()->assistantJuridique()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $this->actingAs($assistant)
            ->post(route('factures.paiements.store', $facture), ['montant' => 10])
            ->assertForbidden();
    }

    /**
     * Vérifie qu'un paiement valide est enregistré sans payer la facture.
     */
    public function test_valid_payment_is_registered_without_paying_off_facture(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);

        $response = $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 600.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Virement bancaire',
            'reference' => 'REF-123',
        ]);

        $paiement = Paiement::where('facture_id', $facture->id)->firstOrFail();

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Le paiement a été enregistré avec succès.');
        $this->assertModelExists($paiement);
        $this->assertSame('600.00', (string) $paiement->montant);
        $this->assertSame('REF-123', $paiement->reference);
        $this->assertSame('Non payée', $facture->fresh()->statut);
    }

    /**
     * Vérifie que la facture passe au statut payée quand le solde est couvert.
     */
    public function test_facture_becomes_payee_when_fully_paid(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);

        $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 600.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Chèque',
        ]);
        $this->assertSame('Non payée', $facture->fresh()->statut);

        $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 400.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Chèque',
        ]);

        $this->assertSame('Payée', $facture->fresh()->statut);
    }

    /**
     * Vérifie que le paiement est refusé lorsqu'il dépasse le montant restant dû.
     */
    public function test_payment_over_the_remaining_amount_is_rejected(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);

        $response = $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 1200.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Espèces',
        ]);

        $response->assertSessionHasErrors('montant', 'Le montant payé ne peut pas dépasser le montant restant dû (1 000,00 €).');
        $this->assertDatabaseCount('paiements', 0);
        $this->assertSame('Non payée', $facture->fresh()->statut);
    }

    /**
     * Vérifie que la création exige le montant, la date et le mode de paiement.
     */
    public function test_paiement_store_requires_montant_date_and_mode(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), []);

        $response->assertSessionHasErrors(['montant', 'date_paiement', 'mode_paiement']);
        $this->assertDatabaseCount('paiements', 0);
    }

    /**
     * Vérifie que la création refuse un mode de paiement inconnu.
     */
    public function test_paiement_store_rejects_unknown_mode(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create();

        $response = $this->actingAs($avocat)->post(route('factures.paiements.store', $facture), [
            'montant' => 100.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'PayPal',
        ]);

        $response->assertSessionHasErrors('mode_paiement', 'Le mode de paiement choisi n\'est pas valide.');
        $this->assertDatabaseCount('paiements', 0);
    }

    /**
     * Vérifie que la suppression d'un paiement revient au statut non payée.
     */
    public function test_deleting_a_payment_reverts_facture_to_non_payee(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);
        $paiement = Paiement::factory()->create([
            'facture_id' => $facture->id,
            'montant' => 1000.00,
        ]);
        $facture->update(['statut' => 'Payée']);

        $response = $this->actingAs($avocat)->delete(route('factures.paiements.destroy', [$facture, $paiement]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Le paiement a été supprimé.');
        $this->assertDatabaseMissing('paiements', ['id' => $paiement->id]);
        $this->assertSame('Non payée', $facture->fresh()->statut);
    }

    /**
     * Vérifie qu'un paiement peut être supprimé même si la facture reste impayée.
     */
    public function test_deleting_a_partial_payment_keeps_facture_non_payee(): void
    {
        $avocat = User::factory()->avocat()->create();
        $facture = Facture::factory()->nonPayee()->create(['montant' => 1000.00]);
        $premier = Paiement::factory()->create([
            'facture_id' => $facture->id,
            'montant' => 200.00,
        ]);
        $second = Paiement::factory()->create([
            'facture_id' => $facture->id,
            'montant' => 800.00,
        ]);
        $facture->update(['statut' => 'Payée']);

        $this->actingAs($avocat)->delete(route('factures.paiements.destroy', [$facture, $second]));

        $this->assertDatabaseMissing('paiements', ['id' => $second->id]);
        $this->assertModelExists($premier->fresh());
        $this->assertSame('Non payée', $facture->fresh()->statut);
    }
}
