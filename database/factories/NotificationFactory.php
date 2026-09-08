<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['Audience', 'Paiement', 'Interne']);
        $titres = [
            'Audience' => 'Rappel : Audience programmée prochainement',
            'Paiement' => 'Nouvel encaissement ou facture impayée',
            'Interne' => 'Mise à jour d\'un dossier ou nouvelle note',
        ];

        return [
            'titre' => $titres[$type],
            'message' => fake()->paragraph(2),
            'type' => $type,
            'lu' => fake()->boolean(30),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Notification déjà lue.
     */
    public function lue(): static
    {
        return $this->state(fn (array $attributes) => [
            'lu' => true,
        ]);
    }

    /**
     * Notification non lue.
     */
    public function nonLue(): static
    {
        return $this->state(fn (array $attributes) => [
            'lu' => false,
        ]);
    }

    /**
     * Notification de type Audience.
     */
    public function audience(): static
    {
        return $this->state(fn (array $attributes) => [
            'titre' => 'Rappel : Audience programmée au tribunal',
            'type' => 'Audience',
        ]);
    }

    /**
     * Notification de type Paiement.
     */
    public function paiement(): static
    {
        return $this->state(fn (array $attributes) => [
            'titre' => 'Avis de règlement d\'honoraires',
            'type' => 'Paiement',
        ]);
    }
}
