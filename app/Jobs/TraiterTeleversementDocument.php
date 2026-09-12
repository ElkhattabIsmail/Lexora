<?php

namespace App\Jobs;

use App\Models\Dossier;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TraiterTeleversementDocument implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $dossierId,
        public int $utilisateurId,
        public string $cheminTemporaire,
        public string $nom,
        public string $type,
    ) {}

    public function handle(): void
    {
        $dossier = Dossier::find($this->dossierId);

        if (! $dossier) {
            Log::warning("Dossier introuvable pour le téléversement : {$this->dossierId}");

            return;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->cheminTemporaire)) {
            Log::warning("Fichier temporaire introuvable : {$this->cheminTemporaire}");

            return;
        }

        $cheminFinal = 'documents/'.$this->dossierId.'/'.basename($this->cheminTemporaire);

        if (! $disk->move($this->cheminTemporaire, $cheminFinal)) {
            Log::error("Impossible de déplacer le fichier : {$this->cheminTemporaire}");

            return;
        }

        $document = $dossier->documents()->create([
            'nom' => $this->nom,
            'chemin' => $cheminFinal,
            'type' => $this->type,
            'taille' => $disk->size($cheminFinal),
            'uploaded_by' => $this->utilisateurId,
        ]);

        $dossier->enregistrerAction("Document téléversé : {$document->nom}", User::findOrFail($this->utilisateurId));
    }
}
