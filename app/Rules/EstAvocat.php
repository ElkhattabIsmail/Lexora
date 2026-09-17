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
     * @param  string   $attribute  The name of the field being validated (e.g. "avocat_id").
     * @param  mixed    $value      The submitted value (expected to be a User primary key).
     * @param  Closure  $fail       Call $fail('message') to report a validation failure.
     *                              Do nothing to indicate the value is valid.
     *
     * Logic: queries the users table to confirm that a User with the given ID
     * exists AND has a role named "Avocat" via a whereHas relationship check.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $estAvocat = User::whereKey($value)
            ->whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))
            ->exists();

        if (! $estAvocat) {
            $fail('L\'avocat sélectionné n\'existe pas ou ne possède pas le rôle Avocat.');
        }
    }
}
