<?php

namespace Tests\Feature;

use App\Models\Audience;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Facture;
use App\Models\Historique;
use App\Models\Paiement;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NPlusOneDetectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Exécute une action en comptant les requêtes SQL exécutées pendant la requête HTTP,
     * puis vérifie que le total reste en dessous du budget fixé. Un chargement paresseux
     * répété en boucle (problème N+1) ferait dépasser ce budget dès que le jeu de données grossit.
     */
    private function assertRequetesBornees(int $budget, Closure $action): void
    {
        $compteur = 0;

        DB::listen(function () use (&$compteur): void {
            $compteur++;
        });

        $action();

        $this->assertLessThanOrEqual($budget, $compteur, "{$compteur} requête(s) SQL exécutée(s) pour un budget de {$budget}.");
    }

    /**
     * Génère un jeu de données réaliste : 10 clients, 20 dossiers, audiences,
     * documents, historiques, factures et paiements.
     *
     * @return array{client: Client, dossier: Dossier, facture: Facture}
     */
    private function peuplerBase(): array
    {
        $avocat = User::factory()->avocat()->create();
        $dossiers = [];
        $factures = [];

        foreach (Client::factory()->count(10)->particulier()->create() as $client) {
            foreach (Dossier::factory()->count(2)->enCours()->create([
                'client_id' => $client->id,
                'avocat_id' => $avocat->id,
            ]) as $dossier) {
                $dossiers[] = $dossier;

                Audience::factory()->prevue()->create([
                    'dossier_id' => $dossier->id,
                    'avocat_id' => $avocat->id,
                ]);

                Document::factory()->create([
                    'dossier_id' => $dossier->id,
                    'uploaded_by' => $avocat->id,
                ]);

                Historique::factory()->create([
                    'dossier_id' => $dossier->id,
                    'user_id' => $avocat->id,
                ]);

                $factures[] = Facture::factory()->create(['client_id' => $client->id]);
            }
        }

        foreach ($factures as $facture) {
            Paiement::factory()->count(2)->create(['facture_id' => $facture->id]);
        }

        return [
            'client' => Client::firstOrFail(),
            'dossier' => $dossiers[0],
            'facture' => $factures[0],
        ];
    }

    public function test_welcome_guest_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $this->assertRequetesBornees(5, fn () => $this->get('/')->assertOk());
    }

    public function test_dashboard_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(20, fn () => $this->get(route('dashboard'))->assertOk());
    }

    public function test_clients_index_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(10, fn () => $this->get(route('clients.index'))->assertOk());
    }

    public function test_clients_show_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $donnees = $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(10, fn () => $this->get(route('clients.show', $donnees['client']))->assertOk());
    }

    public function test_dossiers_index_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(12, fn () => $this->get(route('dossiers.index'))->assertOk());
    }

    public function test_dossiers_show_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $donnees = $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(20, fn () => $this->get(route('dossiers.show', $donnees['dossier']))->assertOk());
    }

    public function test_factures_index_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(10, fn () => $this->get(route('factures.index'))->assertOk());
    }

    public function test_factures_show_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        $donnees = $this->peuplerBase();
        $this->actingAs(User::factory()->avocat()->create());

        $this->assertRequetesBornees(12, fn () => $this->get(route('factures.show', $donnees['facture']))->assertOk());
    }

    public function test_admin_users_index_ne_souffre_pas_de_probleme_n_plus_1(): void
    {
        User::factory()->count(10)->create();
        $this->actingAs(User::factory()->administrateur()->create());

        $this->assertRequetesBornees(10, fn () => $this->get(route('admin.users.index'))->assertOk());
    }
}
