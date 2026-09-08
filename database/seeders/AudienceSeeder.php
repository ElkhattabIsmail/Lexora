<?php

namespace Database\Seeders;

use App\Models\Audience;
use App\Models\Dossier;
use Illuminate\Database\Seeder;

class AudienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dossiers = Dossier::with('avocat')->get();

        if ($dossiers->isEmpty()) {
            return;
        }

        $tribunaux = [
            'Tribunal de Première Instance de Casablanca',
            'Cour d\'Appel de Casablanca',
            'Tribunal de Commerce de Casablanca',
            'Tribunal de Première Instance de Rabat',
            'Cour d\'Appel de Rabat',
            'Tribunal Administratif de Rabat',
        ];

        foreach ($dossiers as $dossier) {
            // Audience passée (Terminée)
            Audience::create([
                'date' => fake()->dateTimeBetween('-4 months', '-1 week')->format('Y-m-d'),
                'heure' => fake()->randomElement(['09:00:00', '09:30:00', '10:00:00', '11:00:00']),
                'tribunal' => fake()->randomElement($tribunaux),
                'observations' => 'Plaidoiries entendues, affaire renvoyée pour échange de conclusions.',
                'statut' => 'Terminée',
                'dossier_id' => $dossier->id,
                'avocat_id' => $dossier->avocat_id,
            ]);

            // Pour les dossiers en cours, prévoir une audience future
            if ($dossier->isEnCours()) {
                Audience::create([
                    'date' => fake()->dateTimeBetween('+3 days', '+2 months')->format('Y-m-d'),
                    'heure' => fake()->randomElement(['09:30:00', '10:30:00', '14:30:00']),
                    'tribunal' => fake()->randomElement($tribunaux),
                    'observations' => 'Audience de mise en état - Présentation des pièces justificatives.',
                    'statut' => 'Prévue',
                    'dossier_id' => $dossier->id,
                    'avocat_id' => $dossier->avocat_id,
                ]);
            }
        }
    }
}
