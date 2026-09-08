<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'message',
        'type',
        'lu',
        'user_id',
    ];

    protected $casts = [
        'lu' => 'boolean',
    ];

    /**
     * L'utilisateur destinataire de la notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marque la notification comme lue.
     */
    public function marquerCommeLue(): void
    {
        $this->update(['lu' => true]);
    }

    /**
     * Vérifie si la notification n'a pas encore été lue.
     */
    public function isNonLue(): bool
    {
        return $this->lu === false;
    }
}
