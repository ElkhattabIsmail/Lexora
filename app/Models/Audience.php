<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audience Model — represents a scheduled court hearing.
 *
 * An audience is always linked to a Dossier (legal case) and to the
 * lawyer (avocat) who will appear in court. It tracks the tribunal,
 * date/time, and current status of the hearing.
 *
 * Relationships:
 *   - belongsTo Dossier  (the case this hearing is for)
 *   - belongsTo User     (the lawyer attending, via avocat_id)
 */
class Audience extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',         // Hearing date (Carbon date)
        'heure',        // Hearing time as string e.g. "09:30"
        'tribunal',     // Name/location of the court
        'observations', // Free-text notes about the hearing
        'statut',       // "Prévue" | "Terminée" | "Annulée"
        'dossier_id',   // FK → dossiers.id
        'avocat_id',    // FK → users.id (must have role Avocat)
    ];

    /**
     * Automatic type casts.
     * - date → Carbon date instance (no time component stored here; time is in 'heure').
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The legal case (dossier) this hearing belongs to.
     *
     * Usage : $audience->dossier->numero_dossier
     * Similar: Document::dossier(), Historique::dossier() — same belongsTo(Dossier).
     *
     * @return BelongsTo<Dossier, $this>
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * The lawyer (User with role Avocat) assigned to this hearing.
     * Uses 'avocat_id' as the foreign key instead of the default 'user_id'.
     *
     * Usage : $audience->avocat->nom_complet
     * Similar: Dossier::avocat() — identical avocat_id foreign key pattern.
     *
     * @return BelongsTo<User, $this>
     */
    public function avocat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avocat_id');
    }

    // =========================================================================
    // State helpers
    // =========================================================================

    /**
     * Returns true when this hearing has been cancelled.
     *
     * Usage : if ($audience->isAnnulee()) { ... }
     * Similar: isPrevue() — mutually exclusive counterpart.
     */
    public function isAnnulee(): bool
    {
        return $this->statut === 'Annulée';
    }

    /**
     * Returns true when this hearing is still scheduled (not yet held or cancelled).
     *
     * Usage : if ($audience->isPrevue()) { ... }
     * Similar: isAnnulee() — mutually exclusive counterpart.
     */
    public function isPrevue(): bool
    {
        return $this->statut === 'Prévue';
    }
}
