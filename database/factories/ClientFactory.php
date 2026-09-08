<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['Particulier', 'Entreprise']);
        $isEntreprise = $type === 'Entreprise';

        return [
            'nom' => $isEntreprise ? fake()->company() : fake()->lastName(),
            'prenom' => $isEntreprise ? null : fake()->firstName(),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'adresse' => fake()->streetAddress().', '.fake()->city(),
            'type' => $type,
        ];
    }

    /**
     * Client particulier.
     */
    public function particulier(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'type' => 'Particulier',
        ]);
    }

    /**
     * Client entreprise.
     */
    public function entreprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'nom' => fake()->company(),
            'prenom' => null,
            'type' => 'Entreprise',
        ]);
    }
}
