<?php

namespace Database\Factories;

use App\Models\Audience;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audience>
 */
class AudienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'heure' => fake()->randomElement(['09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '14:00:00', '14:30:00', '15:00:00']),
            'tribunal' => fake()->randomElement([
                'Tribunal de Première Instance de Casablanca',
                'Tribunal de Première Instance de Rabat',
                'Cour d\'Appel de Casablanca',
                'Cour d\'Appel de Rabat',
                'Tribunal de Commerce de Casablanca',
                'Tribunal Administratif de Rabat',
            ]),
            'observations' => fake()->optional(0.7)->sentence(),
            'statut' => fake()->randomElement(['Prévue', 'Annulée', 'Terminée']),
            'dossier_id' => Dossier::factory(),
            'avocat_id' => fn (array $attributes) => Dossier::find($attributes['dossier_id'])?->avocat_id ?? User::factory()->avocat(),
        ];
    }

    /**
     * Audience prévue.
     */
    public function prevue(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Prévue',
            'date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
        ]);
    }

    /**
     * Audience terminée.
     */
    public function terminee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Terminée',
            'date' => fake()->dateTimeBetween('-3 months', 'yesterday')->format('Y-m-d'),
        ]);
    }

    /**
     * Audience annulée.
     */
    public function annulee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Annulée',
            'observations' => 'Audience annulée ou renvoyée par le tribunal.',
        ]);
    }
}
