<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * EstAvocat — custom validation rule that checks whether a user ID belongs to
 * a user whose role is "Avocat".
 *
 * Used in:
 *   - StoreDossierRequest   for the 'avocat_id' field.
 *   - UpdateDossierRequest  for the 'avocat_id' field.
 *
 * This prevents accidentally assigning a dossier to an administrator or
 * an assistant juridique who is not qualified to act as the case lawyer.
 */
class EstAvocat implements ValidationRule
{
    /**
     * Runs the validation logic for this rule.
     *
     * @param  string  $attribute  The name of the field being validated (e.g. "avocat_id").
     * @param  mixed  $value  The submitted value (expected to be a User primary key).
     * @param  Closure  $fail  Call $fail('message') to report a validation failure.
     *                         Do nothing to indicate the value is valid.
     *
     * Logic: queries the users table to confirm that a User with the given ID
     * exists AND has a role named "Avocat" via a whereHas relationship check.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // ---------------------------------------------------------------------
        // whereKey($value) :
        // Méthode Eloquent qui cible automatiquement la clé primaire du modèle (ici 'users.id').
        // Produit la clause SQL : WHERE `users`.`id` = ?
        // Avantage : fonctionne quel que soit le nom de la clé primaire (id, uuid, etc.).
        // ---------------------------------------------------------------------
        $estAvocat = User::whereKey($value)
            // -----------------------------------------------------------------
            // whereHas('role', Closure) :
            // Vérifie l'existence d'une relation avec la table 'roles'.
            // Ne sélectionne que les utilisateurs dont le rôle porte le nom 'Avocat'.
            // Produit une sous-requête SQL : AND EXISTS (SELECT * FROM `roles` ...)
            // -----------------------------------------------------------------
            ->whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))
            // -----------------------------------------------------------------
            // exists() :
            // Exécute un "SELECT EXISTS(...)" optimisé au niveau de la base de données.
            // Retourne immédiatement un booléen (true/false) sans charger d'objet en mémoire.
            // -----------------------------------------------------------------
            ->exists();

        // ---------------------------------------------------------------------
        // Condition d'échec de la règle de validation :
        // Si l'utilisateur n'existe pas ou ne possède pas le rôle 'Avocat' ($estAvocat === false).
        // ---------------------------------------------------------------------
        if (! $estAvocat) {
            // -----------------------------------------------------------------
            // $fail($message) :
            // Closure injectée par Laravel pour enregistrer l'erreur de validation.
            // Le message sera rattaché au champ (ex: 'avocat_id') et affiché dans le formulaire.
            // -----------------------------------------------------------------
            $fail('L\'avocat sélectionné n\'existe pas ou ne possède pas le rôle Avocat.');
        }
    }
}
