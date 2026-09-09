<?php

namespace App\Console\Commands;

use App\Models\Audience;
use App\Models\Notification;
use Illuminate\Console\Command;

class RappelAudiences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiences:rappel {--horizon=3 : Nombre de jours avant l\'audience pour envoyer le rappel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère une notification de rappel pour les audiences à venir.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $horizon = (int) $this->option('horizon');
        $cible = now()->addDays($horizon)->endOfDay();

        $audiences = Audience::with('dossier')
            ->where('statut', 'Prévue')
            ->whereBetween('date', [now()->startOfDay(), $cible])
            ->get();

        $notificationsCreees = 0;

        foreach ($audiences as $audience) {
            if (! $audience->avocat_id || ! $audience->dossier) {
                continue;
            }

            $message = "Rappel : audience « {$audience->tribunal} » prévue le "
                .$audience->date->format('d/m/Y')." à {$audience->heure} "
                ."pour le dossier {$audience->dossier->numero_dossier}.";

            $existe = Notification::where('user_id', $audience->avocat_id)
                ->where('message', $message)
                ->where('lu', false)
                ->exists();

            if ($existe) {
                continue;
            }

            Notification::create([
                'titre' => 'Audience à venir',
                'message' => $message,
                'type' => 'Audience',
                'lu' => false,
                'user_id' => $audience->avocat_id,
            ]);

            $notificationsCreees++;
        }

        $this->info("{$notificationsCreees} notification(s) de rappel d'audience générée(s).");
    }
}
