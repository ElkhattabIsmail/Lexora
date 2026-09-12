<?php

namespace Tests\Feature;

use App\Jobs\GenererRappelsAudience;
use App\Jobs\SupprimerDocumentsDossier;
use App\Jobs\TraiterTeleversementDocument;
use App\Models\Audience;
use App\Models\Dossier;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueueJobsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que le job de téléversement déplace le fichier et crée le document (RG48).
     */
    public function test_upload_job_moves_file_and_creates_document(): void
    {
        Storage::fake('public');

        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);
        $fichier = UploadedFile::fake()->create('contrat.pdf', 100, 'application/pdf');

        $this->actingAs($avocat)->post(route('dossiers.documents.store', $dossier), [
            'fichier' => $fichier,
            'type' => 'Contrat',
        ]);

        $document = $dossier->documents()->firstOrFail();

        Storage::disk('public')->assertExists($document->chemin);
        $this->assertStringStartsWith("documents/{$dossier->id}/", $document->chemin);
        $this->assertSame('Contrat', $document->type);
        $this->assertSame($avocat->id, $document->uploaded_by);
        $this->assertDatabaseHas('historiques', [
            'dossier_id' => $dossier->id,
            'user_id' => $avocat->id,
            'action' => 'Document téléversé : contrat.pdf',
        ]);
    }

    /**
     * Vérifie que le job de téléversement récupère le dossier manquant sans planter.
     */
    public function test_upload_job_is_resilient_when_dossier_is_missing(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tmp/999/contrat.pdf', 'contenu');

        TraiterTeleversementDocument::dispatch(
            dossierId: 999,
            utilisateurId: 1,
            cheminTemporaire: 'tmp/999/contrat.pdf',
            nom: 'contrat.pdf',
            type: 'Contrat',
        );

        $this->assertSame(0, Dossier::count());
    }

    /**
     * Vérifie que la suppression d'un dossier supprime les fichiers physiques du dossier.
     */
    public function test_dossier_destroy_purges_physical_files(): void
    {
        Storage::fake('public');

        $avocat = User::factory()->avocat()->create();
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        Storage::disk('public')->put("documents/{$dossier->id}/contrat.pdf", 'contenu');
        Storage::disk('public')->put("tmp/{$dossier->id}/brouillon.pdf", 'brouillon');

        $this->actingAs($avocat)->delete(route('dossiers.destroy', $dossier));

        Storage::disk('public')->assertMissing("documents/{$dossier->id}/contrat.pdf");
        Storage::disk('public')->assertMissing("tmp/{$dossier->id}/brouillon.pdf");
        $this->assertDatabaseMissing('dossiers', ['id' => $dossier->id]);
    }

    /**
     * Vérifie que le job de purge est bien le seul nettoyeur de fichiers physiques.
     */
    public function test_supprimer_documents_dossier_job_deletes_directory_content(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/42/contrat.pdf', 'contenu');

        SupprimerDocumentsDossier::dispatch(42);

        Storage::disk('public')->assertMissing('documents/42/contrat.pdf');
    }

    /**
     * Vérifie que la commande audiences:rappel se limite à dispatcher le job.
     */
    public function test_rapel_command_dispatches_job_with_horizon(): void
    {
        Queue::fake();

        $this->artisan('audiences:rappel', ['--horizon' => 5])
            ->assertSuccessful();

        Queue::assertPushed(GenererRappelsAudience::class, fn ($job) => $job->horizon === 5);
    }

    /**
     * Vérifie que le job de rappel crée une notification par audience candidate (RG30).
     */
    public function test_rapel_job_creates_notification(): void
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

        GenererRappelsAudience::dispatch(horizon: 3);

        $notification = Notification::where('user_id', $avocat->id)->firstOrFail();
        $this->assertSame('Audience à venir', $notification->titre);
        $this->assertStringContainsString($dossier->numero_dossier, $notification->message);
    }
}
