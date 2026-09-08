<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $templates = [
            [
                'titre' => 'Audience de plaidoirie prévue',
                'message' => 'Une audience de plaidoirie est programmée au Tribunal de Commerce dans 3 jours.',
                'type' => 'Audience',
            ],
            [
                'titre' => 'Règlement d\'honoraires reçu',
                'message' => 'Le virement bancaire pour la facture relative au dossier a été encaissé avec succès.',
                'type' => 'Paiement',
            ],
            [
                'titre' => 'Nouvelle note interne au dossier',
                'message' => 'L\'assistant juridique a indexé de nouveaux documents de preuve pour votre revue.',
                'type' => 'Interne',
            ],
            [
                'titre' => 'Échéance légale imminente',
                'message' => 'Rappel : Le délai d\'appel pour le jugement expire sous quinzaine.',
                'type' => 'Interne',
            ],
        ];

        foreach ($users as $user) {
            foreach ($templates as $index => $tpl) {
                Notification::create([
                    'titre' => $tpl['titre'],
                    'message' => $tpl['message'],
                    'type' => $tpl['type'],
                    'lu' => $index % 2 === 0, // Mix de lues et non lues
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
