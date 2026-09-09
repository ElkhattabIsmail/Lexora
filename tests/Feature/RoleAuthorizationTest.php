<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie le bon fonctionnement des méthodes helpers de rôles sur User.
     */
    public function test_user_role_helper_methods(): void
    {
        $roleAdmin = Role::factory()->administrateur()->create();
        $roleAvocat = Role::factory()->avocat()->create();
        $roleAssistant = Role::factory()->assistantJuridique()->create();

        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $avocat = User::factory()->create(['role_id' => $roleAvocat->id]);
        $assistant = User::factory()->create(['role_id' => $roleAssistant->id]);

        $this->assertTrue($admin->isAdministrateur());
        $this->assertFalse($admin->isAvocat());
        $this->assertFalse($admin->isAssistantJuridique());
        $this->assertTrue($admin->hasRole('Administrateur'));
        $this->assertTrue($admin->hasRole(['Administrateur', 'Avocat']));

        $this->assertTrue($avocat->isAvocat());
        $this->assertFalse($avocat->isAdministrateur());
        $this->assertTrue($avocat->hasRole('Avocat'));

        $this->assertTrue($assistant->isAssistantJuridique());
        $this->assertFalse($assistant->isAvocat());
        $this->assertTrue($assistant->hasRole('Assistant Juridique'));
    }

    /**
     * Vérifie que les invités sont redirigés vers la page de connexion.
     */
    public function test_guests_are_redirected_from_admin_routes(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }

    /**
     * Vérifie qu'un avocat reçoit une erreur 403 sur l'espace d'administration.
     */
    public function test_avocat_cannot_access_admin_routes(): void
    {
        $roleAvocat = Role::factory()->avocat()->create();
        $avocat = User::factory()->create(['role_id' => $roleAvocat->id]);

        $response = $this->actingAs($avocat)->get('/admin/users');

        $response->assertForbidden();
    }

    /**
     * Vérifie qu'un assistant juridique reçoit une erreur 403 sur l'espace d'administration.
     */
    public function test_assistant_cannot_access_admin_routes(): void
    {
        $roleAssistant = Role::factory()->assistantJuridique()->create();
        $assistant = User::factory()->create(['role_id' => $roleAssistant->id]);

        $response = $this->actingAs($assistant)->get('/admin/users');

        $response->assertForbidden();
    }

    /**
     * Vérifie qu'un administrateur accède avec succès à la gestion des rôles.
     */
    public function test_administrators_can_access_admin_user_role_management(): void
    {
        $roleAdmin = Role::factory()->administrateur()->create();
        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
        $response->assertViewIs('admin.users.index');
        $response->assertSee('Gestion des utilisateurs et des rôles');
    }

    /**
     * Vérifie qu'un administrateur peut modifier le rôle d'un utilisateur.
     */
    public function test_administrator_can_update_user_role(): void
    {
        $roleAdmin = Role::factory()->administrateur()->create();
        $roleAvocat = Role::factory()->avocat()->create();
        $roleAssistant = Role::factory()->assistantJuridique()->create();

        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $targetUser = User::factory()->create(['role_id' => $roleAssistant->id]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.update-role', $targetUser), [
                'role_id' => $roleAvocat->id,
            ]);

        $response->assertSessionHas('status');
        $response->assertRedirect();

        $this->assertSame($roleAvocat->id, $targetUser->fresh()->role_id);
        $this->assertTrue($targetUser->fresh()->isAvocat());
    }

    /**
     * Vérifie que le seul administrateur ne peut pas être rétrogradé.
     */
    public function test_sole_administrator_cannot_be_demoted(): void
    {
        $roleAdmin = Role::factory()->administrateur()->create();
        $roleAvocat = Role::factory()->avocat()->create();

        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.update-role', $admin), [
                'role_id' => $roleAvocat->id,
            ]);

        $response->assertSessionHasErrors(['role_id']);
        $this->assertSame($roleAdmin->id, $admin->fresh()->role_id);
    }

    /**
     * Vérifie que le tableau de bord affiche les statistiques et les derniers dossiers.
     */
    public function test_avocat_can_view_dashboard_with_recent_dossiers(): void
    {
        $roleAvocat = Role::factory()->avocat()->create();
        $avocat = User::factory()->create(['role_id' => $roleAvocat->id]);
        $dossier = Dossier::factory()->enCours()->create(['avocat_id' => $avocat->id]);

        $response = $this->actingAs($avocat)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSee($dossier->numero_dossier);
        $response->assertSee($dossier->statut);
    }
}
