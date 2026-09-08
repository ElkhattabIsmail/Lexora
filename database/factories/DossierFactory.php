<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dossier>
 */
class DossierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statut = fake()->randomElement(['En cours', 'Gagné', 'Perdu', 'Fermé']);
        $dateOuverture = fake()->dateTimeBetween('-1 year', 'now');
        $dateFermeture = $statut !== 'En cours'
            ? fake()->dateTimeBetween($dateOuverture, 'now')->format('Y-m-d')
            : null;

        return [
            'numero_dossier' => 'DOS-'.fake()->unique()->numerify('2026-####'),
            'type_affaire' => fake()->randomElement([
                'Droit des affaires',
                'Droit du travail',
                'Droit civil',
                'Droit pénal',
                'Droit immobilier',
                'Droit commercial',
                'Droit de la famille',
            ]),
            'statut' => $statut,
            'date_ouverture' => $dateOuverture->format('Y-m-d'),
            'date_fermeture' => $dateFermeture,
            'archive' => $statut === 'Fermé' ? fake()->boolean(60) : false,
            'client_id' => Client::factory(),
            'avocat_id' => User::factory()->avocat(),
        ];
    }

    /**
     * Dossier en cours d'instruction.
     */
    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'En cours',
            'date_fermeture' => null,
            'archive' => false,
        ]);
    }

    /**
     * Dossier gagné.
     */
    public function gagne(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Gagné',
            'date_fermeture' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Dossier perdu.
     */
    public function perdu(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Perdu',
            'date_fermeture' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Dossier fermé / classé.
     */
    public function ferme(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Fermé',
            'date_fermeture' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'archive' => true,
        ]);
    }

    /**
     * Dossier archivé.
     */
    public function archive(): static
    {
        return $this->state(fn (array $attributes) => [
            'archive' => true,
        ]);
    }
}
