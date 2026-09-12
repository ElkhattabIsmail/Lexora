<?php

namespace App\Console\Commands;

use App\Jobs\GenererRappelsAudience;
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

        GenererRappelsAudience::dispatch($horizon);

        $this->info("Génération des rappels d'audience (horizon : {$horizon} jour(s)) envoyée à la file d'attente.");
    }
}
