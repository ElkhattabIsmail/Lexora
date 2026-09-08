<?php

namespace Database\Factories;

use App\Models\Dossier;
use App\Models\Historique;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Historique>
 */
class HistoriqueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => fake()->randomElement([
                'Création et ouverture du dossier',
                'Fixation de la date de la première audience',
                'Dépôt des conclusions écrites au greffe',
                'Réception des pièces de la partie adverse',
                'Téléversement des pièces justificatives du client',
                'Audience de plaidoirie tenue devant le tribunal',
                'Mise en délibéré de l\'affaire',
                'Notification du jugement rendu',
                'Émission de la facture d\'honoraires',
                'Enregistrement de l\'encaissement d\'un paiement',
                'Clôture et archivage du dossier',
            ]),
            'date_action' => fake()->dateTimeBetween('-1 year', 'now'),
            'dossier_id' => Dossier::factory(),
            'user_id' => User::factory(),
        ];
    }
}
