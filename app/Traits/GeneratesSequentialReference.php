<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * GeneratesSequentialReference — provides auto-incrementing reference number generation.
 *
 * Used by DossierController (prefix "DOS") and FactureController (prefix "FAC").
 * The reference format is: PREFIX-YYYY-XXXXX
 *   e.g. DOS-2024-00001, FAC-2024-00042
 *
 * The sequence resets to 00001 at the start of each calendar year.
 */
trait GeneratesSequentialReference
{
    /**
     * Generates the next sequential reference string for the given model and prefix.
     *
     * @param  class-string<Model>  $model   The Eloquent model class to count records for
     *                                       (e.g. Dossier::class, Facture::class).
     * @param  string               $prefix  Short uppercase prefix e.g. "DOS" or "FAC".
     *
     * @return string  Reference in format PREFIX-YYYY-NNNNN, e.g. "DOS-2024-00001".
     *
     * Algorithm:
     *   1. Get the current year.
     *   2. Find the highest existing ID for that model in the current year.
     *   3. Increment by 1 and left-pad to 5 digits.
     *   4. Concatenate as "PREFIX-YEAR-NNNNN".
     *
     * Note: This is NOT strictly unique-safe under concurrent inserts; for a
     * production system with high write volume, a database sequence or atomic lock
     * would be more reliable. For a law firm's typical workload it is sufficient.
     *
     * Called by:
     *   - DossierController::store()  → prefix="DOS"
     *   - FactureController::store()  → prefix="FAC"
     */
    protected function generateSequentialReference(string $model, string $prefix): string
    {
        $year = now()->year;
        $last = $model::whereYear('created_at', $year)->max('id') ?? 0;
        $sequence = str_pad($last + 1, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }
}
