<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facture>
 */
class FactureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_facture' => 'FAC-'.now()->year.'-'.fake()->unique()->numberBetween(90001, 99999),
            'montant' => fake()->randomFloat(2, 1500, 35000),
            'date_facture' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'statut' => fake()->randomElement(['Payée', 'Non payée']),
            'client_id' => Client::factory(),
            'dossier_id' => fn (array $attributes) => Dossier::factory()->create(['client_id' => $attributes['client_id']])->id,
        ];
    }

    /**
     * Facture payée.
     */
    public function payee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Payée',
        ]);
    }

    /**
     * Facture non payée.
     */
    public function nonPayee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Non payée',
        ]);
    }
}
