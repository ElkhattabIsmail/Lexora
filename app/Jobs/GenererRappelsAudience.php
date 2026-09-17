<?php

namespace App\Jobs;

use App\Models\Audience;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * GenererRappelsAudience Job — generates in-app reminder notifications for upcoming hearings.
 *
 * Dispatched by the scheduled command/task runner (e.g. daily via Artisan schedule).
 * Queries for all "Prévue" audiences within the next $horizon days and creates
 * a Notification for each responsible lawyer — skipping any that have already
 * been sent (deduplication check against unread notifications with the same message).
 *
 * @property int $horizon  Number of days ahead to look for upcoming hearings.
 *                         E.g. horizon=1 → sends reminders for hearings due today or tomorrow.
 */
class GenererRappelsAudience implements ShouldQueue
{
    use Queueable;

    /**
     * Creates the job with the look-ahead horizon.
     *
     * @param  int  $horizon  Number of days ahead to check for upcoming hearings.
     *                        Injected automatically from the dispatch call.
     */
    public function __construct(public int $horizon) {}

    /**
     * Executes the job on the queue worker.
     *
     * Steps:
     *   1. Calculate the upper date bound (today + $horizon days, end of day).
     *   2. Load all "Prévue" audiences scheduled within [now, $cible].
     *   3. Build a list of candidate notifications (one per lawyer per hearing).
     *   4. Load existing unread notifications matching the same user_id + message
     *      to avoid duplicate alerts.
     *   5. Insert only genuinely new notifications.
     *
     * Similar: TraiterTeleversementDocument::handle() — both are queue jobs that
     *          create database records after performing checks.
     */
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
