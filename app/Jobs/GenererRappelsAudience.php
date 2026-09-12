<?php

namespace App\Jobs;

use App\Models\Audience;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenererRappelsAudience implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $horizon) {}

    public function handle(): void
    {
        $cible = now()->addDays($this->horizon)->endOfDay();

        $audiences = Audience::with('dossier')
            ->where('statut', 'Prévue')
            ->whereBetween('date', [now()->startOfDay(), $cible])
            ->get();

        $candidats = [];

        foreach ($audiences as $audience) {
            if (! $audience->avocat_id || ! $audience->dossier) {
                continue;
            }

            $message = "Rappel : audience « {$audience->tribunal} » prévue le "
                .$audience->date->format('d/m/Y')." à {$audience->heure} "
                ."pour le dossier {$audience->dossier->numero_dossier}.";

            $candidats[] = [
                'titre' => 'Audience à venir',
                'message' => $message,
                'type' => 'Audience',
                'lu' => false,
                'user_id' => $audience->avocat_id,
            ];
        }

        if ($candidats === []) {
            return;
        }

        $dejaEnvoyes = Notification::query()
            ->where('lu', false)
            ->whereIn('user_id', array_column($candidats, 'user_id'))
            ->whereIn('message', array_column($candidats, 'message'))
            ->get(['user_id', 'message'])
            ->mapWithKeys(fn (Notification $notification) => [
                $notification->user_id.'|'.$notification->message => true,
            ]);

        foreach ($candidats as $candidat) {
            if ($dejaEnvoyes->has($candidat['user_id'].'|'.$candidat['message'])) {
                continue;
            }

            Notification::create($candidat);
        }
    }
}
