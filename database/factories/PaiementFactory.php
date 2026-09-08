<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mode = fake()->randomElement(['Virement bancaire', 'Chèque', 'Espèces', 'Carte bancaire']);

        return [
            'montant' => fake()->randomFloat(2, 500, 15000),
            'date_paiement' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'mode_paiement' => $mode,
            'reference' => $mode !== 'Espèces' ? 'REF-'.fake()->bothify('??-#####') : null,
            'facture_id' => Facture::factory(),
        ];
    }
}
