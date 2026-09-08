<?php

namespace Database\Seeders;

use App\Models\Dossier;
use App\Models\Historique;
use App\Models\User;
use Illuminate\Database\Seeder;

class HistoriqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dossiers = Dossier::all();
        $users = User::all();

        if ($dossiers->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($dossiers as $dossier) {
            $user = $dossier->avocat_id ? User::find($dossier->avocat_id) : $users->random();

            // 1. Événement d'ouverture
            Historique::create([
                'action' => 'Ouverture officielle du dossier et constitution du mandat.',
                'date_action' => $dossier->date_ouverture->format('Y-m-d').' 09:00:00',
                'dossier_id' => $dossier->id,
                'user_id' => $user->id,
            ]);

            // 2. Événement de gestion
            Historique::create([
                'action' => 'Dépôt des écritures et pièces probantes au greffe de la juridiction compétente.',
                'date_action' => fake()->dateTimeBetween($dossier->date_ouverture, 'now'),
                'dossier_id' => $dossier->id,
                'user_id' => $user->id,
            ]);

            // 3. Événement de clôture si applicable
            if ($dossier->date_fermeture) {
                Historique::create([
                    'action' => 'Décision définitive intervenue. Clôture administrative du dossier.',
                    'date_action' => $dossier->date_fermeture->format('Y-m-d').' 17:00:00',
                    'dossier_id' => $dossier->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
