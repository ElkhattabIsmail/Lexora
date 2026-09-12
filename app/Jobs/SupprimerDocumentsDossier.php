<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class SupprimerDocumentsDossier implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $dossierId) {}

    public function handle(): void
    {
        $disk = Storage::disk('public');

        $disk->deleteDirectory("documents/{$this->dossierId}");
        $disk->deleteDirectory("tmp/{$this->dossierId}");
    }
}
