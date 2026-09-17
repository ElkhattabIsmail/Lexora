<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Historique Model — an immutable activity-log entry for a legal case.
 *
 * Every significant action on a Dossier (creation, status change, document
 * upload, payment, hearing) creates one Historique row via
 * Dossier::enregistrerAction(). This provides a complete audit trail.
 *
 * Relationships:
 *   - belongsTo Dossier  (the case this log entry belongs to)
 *   - belongsTo User     (the staff member who performed the action)
 */
class Historique extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'action',      // Human-readable description e.g. "Statut modifié : En cours → Gagné"
        'date_action', // Timestamp when the action occurred
        'dossier_id',  // FK → dossiers.id
        'user_id',     // FK → users.id
    ];

    /**
     * Automatic type casts.
     * - date_action → Carbon datetime instance (includes time, unlike date-only casts).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_action' => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The legal case (dossier) this log entry is associated with.
     *
     * Usage : $historique->dossier->numero_dossier
     * Similar: Audience::dossier(), Document::dossier() — same belongsTo(Dossier).
     *
     * @return BelongsTo<Dossier, $this>
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * The staff member (User) who performed the logged action.
     *
     * Usage : $historique->user->nom_complet
     * Similar: Notification::user() — same belongsTo(User) pattern.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
