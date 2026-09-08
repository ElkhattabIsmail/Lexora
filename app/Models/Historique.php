<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historique extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'date_action',
        'dossier_id',
        'user_id',
    ];

    protected $casts = [
        'date_action' => 'datetime',
    ];

    /**
     * Le dossier concerné par cette entrée d'historique.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * L'utilisateur ayant effectué l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
