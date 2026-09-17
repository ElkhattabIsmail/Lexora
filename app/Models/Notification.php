<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification Model — an in-app alert for a staff member.
 *
 * Notifications are created by the GenererRappelsAudience background job
 * to remind lawyers of upcoming court hearings. They can be marked as read
 * once the user has seen them.
 *
 * Relationships:
 *   - belongsTo User  (the recipient of the notification)
 */
class Notification extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'titre',   // Short notification title e.g. "Audience à venir"
        'message', // Full notification body text
        'type',    // Category e.g. "Audience" (used for icon/colour differentiation)
        'lu',      // Boolean — true once the user has read it
        'user_id', // FK → users.id (the recipient)
    ];

    /**
     * Automatic type casts.
     * - lu → native PHP bool (stored as 0/1 in the database).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lu' => 'boolean',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * The staff member (User) who will receive this notification.
     *
     * Usage : $notification->user->nom_complet
     * Similar: Historique::user() — same belongsTo(User) pattern.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // Actions & State helpers
    // =========================================================================

    /**
     * Marks this notification as read by setting 'lu' = true and persisting it.
     *
     * Usage : $notification->marquerCommeLue();
     * Similar: isNonLue() — checks the same 'lu' flag.
     */
    public function marquerCommeLue(): void
    {
        $this->update(['lu' => true]);
    }

    /**
     * Returns true when this notification has NOT yet been read by the user.
     *
     * Usage : if ($notification->isNonLue()) { ... }
     * Similar: marquerCommeLue() — acts on the same 'lu' flag.
     */
    public function isNonLue(): bool
    {
        return $this->lu === false;
    }
}
