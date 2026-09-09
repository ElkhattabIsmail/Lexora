<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_custom_fields(): void
    {
        $role = Role::firstOrCreate(['nom' => 'Avocat']);

        $response = $this->post('/register', [
            'nom' => 'Berrada',
            'prenom' => 'Youssef',
            'telephone' => '+212 6 00 11 22 33',
            'role_id' => $role->id,
            'email' => 'youssef.berrada@lexora.ma',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'youssef.berrada@lexora.ma')->first();
        $this->assertNotNull($user);
        $this->assertSame('Berrada', $user->nom);
        $this->assertSame('Youssef', $user->prenom);
        $this->assertSame($role->id, $user->role_id);
    }

    public function test_new_users_can_register_with_legacy_name(): void
    {
        Role::firstOrCreate(['nom' => 'Avocat']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Test', $user->prenom);
        $this->assertSame('User', $user->nom);
    }
}
