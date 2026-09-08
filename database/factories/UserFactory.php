<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => Role::factory(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Utilisateur avec rôle Administrateur.
     */
    public function administrateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstWhere('nom', 'Administrateur')?->id ?? Role::factory()->administrateur(),
        ]);
    }

    /**
     * Utilisateur avec rôle Avocat.
     */
    public function avocat(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstWhere('nom', 'Avocat')?->id ?? Role::factory()->avocat(),
        ]);
    }

    /**
     * Utilisateur avec rôle Assistant Juridique.
     */
    public function assistantJuridique(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstWhere('nom', 'Assistant Juridique')?->id ?? Role::factory()->assistantJuridique(),
        ]);
    }
}
