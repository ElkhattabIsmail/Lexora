<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement(['Administrateur', 'Avocat', 'Assistant Juridique']),
        ];
    }

    /**
     * Rôle Administrateur.
     */
    public function administrateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => 'Administrateur',
        ]);
    }

    /**
     * Rôle Avocat.
     */
    public function avocat(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => 'Avocat',
        ]);
    }

    /**
     * Rôle Assistant Juridique.
     */
    public function assistantJuridique(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => 'Assistant Juridique',
        ]);
    }
}
