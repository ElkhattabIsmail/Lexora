<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EstAvocat implements ValidationRule
{
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
