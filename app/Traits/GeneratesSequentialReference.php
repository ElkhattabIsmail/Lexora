<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait GeneratesSequentialReference
{
    /**
     * Génère une référence séquentielle annuelle au format PREFIX-YYYY-XXXXX.
     *
     * @param  class-string<Model>  $model
     */
    protected function generateSequentialReference(string $model, string $prefix): string
    {
        $year = now()->year;
        $last = $model::whereYear('created_at', $year)->max('id') ?? 0;
        $sequence = str_pad($last + 1, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }
}
