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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    protected Role $roleAvocat;

    protected User $avocat;

    protected Client $client;

    protected Dossier $dossier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleAvocat = Role::create(['nom' => 'Avocat']);
        $roleAdmin = Role::create(['nom' => 'Administrateur']);

        $this->avocat = User::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@lexora.fr',
            'telephone' => '0600000001',
            'password' => bcrypt('password'),
            'role_id' => $this->roleAvocat->id,
        ]);

        $this->client = Client::create([
            'nom' => 'Martin',
            'prenom' => 'Paul',
            'email' => 'paul.martin@example.fr',
            'telephone' => '0600000002',
            'adresse' => '10 rue de la Paix, Paris',
            'type' => 'Particulier',
        ]);

        $this->dossier = Dossier::create([
            'numero_dossier' => 'DOS-2026-001',
            'type_affaire' => 'Droit civil',
            'statut' => 'En cours',
            'date_ouverture' => now()->toDateString(),
            'client_id' => $this->client->id,
            'avocat_id' => $this->avocat->id,
        ]);
    }

    public function test_roles_table_created_and_seeded(): void
    {
        $this->assertDatabaseHas('roles', ['nom' => 'Avocat']);
        $this->assertDatabaseHas('roles', ['nom' => 'Administrateur']);
    }

    public function test_user_belongs_to_role(): void
    {
        $this->assertEquals('Avocat', $this->avocat->role->nom);
        $this->assertTrue($this->avocat->isAvocat());
        $this->assertFalse($this->avocat->isAdministrateur());
    }

    public function test_client_has_dossier(): void
    {
        $this->assertCount(1, $this->client->dossiers);
        $this->assertEquals('DOS-2026-001', $this->client->dossiers->first()->numero_dossier);
    }

    public function test_dossier_belongs_to_client_and_avocat(): void
    {
        $this->assertEquals($this->client->id, $this->dossier->client->id);
        $this->assertEquals($this->avocat->id, $this->dossier->avocat->id);
    }

    public function test_audience_belongs_to_dossier_and_avocat(): void
    {
        $audience = Audience::create([
            'date' => now()->addDays(7)->toDateString(),
            'heure' => '10:00:00',
            'tribunal' => 'Tribunal de Paris',
            'statut' => 'Prévue',
            'dossier_id' => $this->dossier->id,
            'avocat_id' => $this->avocat->id,
        ]);

        $this->assertEquals($this->dossier->id, $audience->dossier->id);
        $this->assertEquals($this->avocat->id, $audience->avocat->id);
        $this->assertTrue($audience->isPrevue());
        $this->assertCount(1, $this->dossier->audiences);
    }

    public function test_document_belongs_to_dossier_and_uploader(): void
    {
        $document = Document::create([
            'nom' => 'Contrat.pdf',
            'chemin' => 'documents/contrat.pdf',
            'type' => 'Contrat',
            'taille' => 204800,
            'dossier_id' => $this->dossier->id,
            'uploaded_by' => $this->avocat->id,
        ]);

        $this->assertEquals($this->dossier->id, $document->dossier->id);
        $this->assertEquals($this->avocat->id, $document->uploader->id);
        $this->assertEquals('200 Ko', $document->taille_formattee);
    }

    public function test_facture_and_paiement_chain(): void
    {
        $facture = Facture::create([
            'numero_facture' => 'FAC-2026-001',
            'montant' => 1500.00,
            'date_facture' => now()->toDateString(),
            'statut' => 'Non payée',
            'client_id' => $this->client->id,
            'dossier_id' => $this->dossier->id,
        ]);

        $paiement = Paiement::create([
            'montant' => 750.00,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'Virement',
            'reference' => 'REF-001',
            'facture_id' => $facture->id,
        ]);

        $this->assertFalse($facture->isPayee());
        $this->assertEquals(750.00, $facture->montant_restant);
        $this->assertCount(1, $facture->paiements);
        $this->assertEquals($facture->id, $paiement->facture->id);
    }

    public function test_historique_belongs_to_dossier_and_user(): void
    {
        $historique = Historique::create([
            'action' => 'Dossier ouvert',
            'date_action' => now(),
            'dossier_id' => $this->dossier->id,
            'user_id' => $this->avocat->id,
        ]);

        $this->assertEquals($this->dossier->id, $historique->dossier->id);
        $this->assertEquals($this->avocat->id, $historique->user->id);
        $this->assertCount(1, $this->dossier->historiques);
    }

    public function test_notification_belongs_to_user(): void
    {
        $notification = Notification::create([
            'titre' => 'Audience demain',
            'message' => 'Rappel : audience le 15/09/2026 à 10h00',
            'type' => 'Audience',
            'lu' => false,
            'user_id' => $this->avocat->id,
        ]);

        $this->assertTrue($notification->isNonLue());
        $notification->marquerCommeLue();
        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'lu' => true]);
        $this->assertEquals($this->avocat->id, $notification->user->id);
    }

    public function test_client_entreprise_nom_complet(): void
    {
        $entreprise = Client::create([
            'nom' => 'Cabinet Martin SARL',
            'type' => 'Entreprise',
        ]);

        $this->assertTrue($entreprise->isEntreprise());
        $this->assertEquals('Cabinet Martin SARL', $entreprise->nom_complet);
    }

    public function test_client_particulier_nom_complet(): void
    {
        $this->assertEquals('Paul Martin', $this->client->nom_complet);
    }
}
