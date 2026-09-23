<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Colonnes modifiables par assignation de masse
    // -------------------------------------------------------------------------
    protected $fillable = [
        'nom', // Nom du rôle : "Administrateur", "Avocat", "Assistant juridique", etc.
    ];

    public function users(): HasMany
    {
        // ---------------------------------------------------------------------
        // Relation HasMany : tous les utilisateurs rattachés à ce rôle
        // SQL : WHERE users.role_id = roles.id
        // ---------------------------------------------------------------------
        return $this->hasMany(User::class);
    }
}
