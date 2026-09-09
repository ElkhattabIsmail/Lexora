<?php

namespace Tests\Feature;

use App\Models\Audience;
use App\Models\Dossier;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RappelAudiencesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la commande génère un rappel pour les audiences à venir sous l'horizon (RG30).
     */
    public function test_command_creates_a_notification_for_upcoming_audience(): void
    {
        $this->travelTo('2026-06-01 08:00:00');

        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
            'date' => '2026-06-03',
            'statut' => 'Prévue',
            'tribunal' => 'Tribunal de Première Instance de Casablanca',
        ]);

        $this->artisan('audiences:rappel', ['--horizon' => 3])
            ->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $avocat->id,
            'type' => 'Audience',
            'lu' => false,
        ]);
        $notification = Notification::where('user_id', $avocat->id)->firstOrFail();
        $this->assertSame('Audience à venir', $notification->titre);
        $this->assertStringContainsString('Rappel : audience « Tribunal de Première Instance de Casablanca »', $notification->message);
        $this->assertStringContainsString('pour le dossier '.$dossier->numero_dossier, $notification->message);
    }

    /**
     * Vérifie que la commande ne crée pas de doublon lors d'un second passage (RG30).
     */
    public function test_command_does_not_create_duplicate_notifications(): void
    {
        $this->travelTo('2026-06-01 08:00:00');

        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
            'date' => '2026-06-03',
            'statut' => 'Prévue',
        ]);

        $this->artisan('audiences:rappel', ['--horizon' => 3])->assertSuccessful();
        $this->artisan('audiences:rappel', ['--horizon' => 3])->assertSuccessful();

        $this->assertSame(1, Notification::where('user_id', $avocat->id)->count());
    }

    /**
     * Vérifie qu'aucun rappel n'est créé pour les audiences passées, annulées ou hors horizon.
     */
    public function test_command_skips_past_cancelled_and_out_of_horizon_audiences(): void
    {
        $this->travelTo('2026-06-01 08:00:00');

        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
            'date' => '2026-05-30',
            'statut' => 'Prévue',
        ]);
        Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
            'date' => '2026-06-02',
            'statut' => 'Annulée',
        ]);
        Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $avocat->id,
            'date' => '2026-06-10',
            'statut' => 'Prévue',
        ]);

        $this->artisan('audiences:rappel', ['--horizon' => 3])
            ->assertSuccessful();

        $this->assertDatabaseCount('notifications', 0);
    }
}
