<?php

namespace Tests\Feature;

use App\Models\Audience;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AudienceCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'un avocat accède au formulaire de création d'audience.
     */
    public function test_avocat_can_open_audience_create_form(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->get(route('dossiers.audiences.create', $dossier));

        $response->assertViewIs('audiences.create');
        $response->assertSee($dossier->numero_dossier);
    }

    /**
     * Vérifie qu'une requête valide crée l'audience et redirige vers le dossier.
     */
    public function test_valid_payload_creates_audience_and_redirects_to_dossier(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->post(route('dossiers.audiences.store', $dossier), [
            'date' => now()->addDays(7)->toDateString(),
            'heure' => '10:00',
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
            'statut' => 'Prévue',
            'observations' => 'Première comparution',
            'avocat_id' => $avocat->id,
        ]);

        $response->assertRedirectToRoute('dossiers.show', $dossier);
        $this->assertDatabaseHas('audiences', [
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
            'statut' => 'Prévue',
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
        ]);
    }

    /**
     * Vérifie que la création refuse un statut d'audience inconnu.
     */
    public function test_audience_store_rejects_unknown_statut(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->post(route('dossiers.audiences.store', $dossier), [
            'date' => now()->addDays(7)->toDateString(),
            'heure' => '10:00',
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
            'statut' => 'Passée',
            'avocat_id' => $avocat->id,
        ]);

        $response->assertSessionHasErrors('statut', 'Le statut doit être : Prévue, Annulée ou Terminée.');
        $this->assertDatabaseMissing('audiences', ['tribunal' => 'Tribunal de Première Instance de Casablanca']);
    }

    /**
     * Vérifie que la création exige date, heure, tribunal et avocat.
     */
    public function test_audience_store_requires_date_heure_tribunal_and_avocat(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->post(route('dossiers.audiences.store', $dossier), []);

        $response->assertSessionHasErrors(['date', 'heure', 'tribunal', 'statut', 'avocat_id']);
    }

    /**
     * Vérifie qu'une requête valide met à jour l'audience.
     */
    public function test_valid_payload_updates_audience(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        $audience = Audience::factory()->prevue()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
        ]);

        $response = $this->actingAs($avocat)->put(route('dossiers.audiences.update', [$dossier, $audience]), [
            'date' => now()->addDays(14)->toDateString(),
            'heure' => '14:30',
            'tribunal' => 'Cour d\'Appel de Casablanca',
            'statut' => 'Terminée',
            'observations' => null,
            'avocat_id' => $avocat->id,
        ]);

        $response->assertRedirectToRoute('dossiers.show', $dossier);
        $this->assertDatabaseHas('audiences', [
            'id' => $audience->id,
            'tribunal' => 'Cour d\'Appel de Casablanca',
            'statut' => 'Terminée',
        ]);
    }

    /**
     * Vérifie qu'une audience peut être supprimée.
     */
    public function test_audience_destroy_removes_audience(): void
    {
        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        $audience = Audience::factory()->prevue()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
        ]);

        $response = $this->actingAs($avocat)->delete(route('dossiers.audiences.destroy', [$dossier, $audience]));

        $response->assertRedirectToRoute('dossiers.show', $dossier);
        $this->assertDatabaseMissing('audiences', ['id' => $audience->id]);
    }
}
