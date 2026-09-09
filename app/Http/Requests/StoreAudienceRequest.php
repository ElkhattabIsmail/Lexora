<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAudienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'heure' => ['required', 'date_format:H:i'],
            'tribunal' => ['required', 'string', 'max:255'],
            'statut' => ['required', 'in:Prévue,Annulée,Terminée'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'avocat_id' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $estAvocat = User::whereKey($value)
                        ->whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))
                        ->exists();

                    if (! $estAvocat) {
                        $fail('L\'avocat sélectionné n\'existe pas ou ne possède pas le rôle Avocat.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'La date de l\'audience est requise.',
            'heure.required' => 'L\'heure est requise.',
            'heure.date_format' => 'L\'heure doit être au format HH:MM.',
            'tribunal.required' => 'Le tribunal est requis.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut doit être : Prévue, Annulée ou Terminée.',
            'avocat_id.required' => 'Un avocat doit être assigné.',
        ];
    }
}
