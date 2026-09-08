<?php

namespace Tests\Feature;

use App\Models\Audience;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Facture;
use App\Models\Historique;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que chaque factory instancie correctement un enregistrement valide.
     */
    public function test_all_factories_can_instantiate_models(): void
    {
        $role = Role::factory()->create();
        $this->assertModelExists($role);

        $user = User::factory()->create(['role_id' => $role->id]);
        $this->assertModelExists($user);

        $client = Client::factory()->create();
        $this->assertModelExists($client);

        $dossier = Dossier::factory()->create([
            'client_id' => $client->id,
            'avocat_id' => $user->id,
        ]);
        $this->assertModelExists($dossier);

        $audience = Audience::factory()->create([
            'dossier_id' => $dossier->id,
            'avocat_id' => $user->id,
        ]);
        $this->assertModelExists($audience);

        $document = Document::factory()->create([
            'dossier_id' => $dossier->id,
            'uploaded_by' => $user->id,
        ]);
        $this->assertModelExists($document);

        $facture = Facture::factory()->create([
            'client_id' => $client->id,
            'dossier_id' => $dossier->id,
        ]);
        $this->assertModelExists($facture);

        $paiement = Paiement::factory()->create([
            'facture_id' => $facture->id,
        ]);
        $this->assertModelExists($paiement);

        $historique = Historique::factory()->create([
            'dossier_id' => $dossier->id,
            'user_id' => $user->id,
        ]);
        $this->assertModelExists($historique);

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->assertModelExists($notification);
    }

    /**
     * Vérifie que DatabaseSeeder peuple toutes les tables sans erreur.
     */
    public function test_database_seeder_populates_all_tables(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Role::count());
        $this->assertGreaterThan(0, User::count());
        $this->assertGreaterThan(0, Client::count());
        $this->assertGreaterThan(0, Dossier::count());
        $this->assertGreaterThan(0, Audience::count());
        $this->assertGreaterThan(0, Document::count());
        $this->assertGreaterThan(0, Facture::count());
        $this->assertGreaterThan(0, Paiement::count());
        $this->assertGreaterThan(0, Historique::count());
        $this->assertGreaterThan(0, Notification::count());
    }

    /**
     * Vérifie que les 3 rôles obligatoires sont bien initialisés.
     */
    public function test_roles_are_seeded_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('roles', ['nom' => 'Administrateur']);
        $this->assertDatabaseHas('roles', ['nom' => 'Avocat']);
        $this->assertDatabaseHas('roles', ['nom' => 'Assistant Juridique']);
    }

    /**
     * Vérifie que les comptes de démonstration existent avec les rôles correspondants.
     */
    public function test_demo_users_are_seeded_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@lexora.ma')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdministrateur());

        $avocat = User::where('email', 'avocat.berrada@lexora.ma')->first();
        $this->assertNotNull($avocat);
        $this->assertTrue($avocat->isAvocat());

        $assistant = User::where('email', 'assistant.idrissi@lexora.ma')->first();
        $this->assertNotNull($assistant);
        $this->assertTrue($assistant->isAssistantJuridique());
    }

    /**
     * Vérifie l'intégrité relationnelle des données générées.
     */
    public function test_data_integrity_and_relationships(): void
    {
        $this->seed(DatabaseSeeder::class);

        $dossier = Dossier::with(['client', 'avocat', 'audiences', 'documents', 'factures'])->first();
        $this->assertNotNull($dossier);
        $this->assertInstanceOf(Client::class, $dossier->client);
        $this->assertInstanceOf(User::class, $dossier->avocat);
        $this->assertTrue($dossier->avocat->isAvocat());

        $facturePayee = Facture::where('statut', 'Payée')->with('paiements')->first();
        if ($facturePayee) {
            $this->assertNotEmpty($facturePayee->paiements);
        }
    }
}
