<?php

namespace App\Http\Requests;

use App\Rules\EstAvocat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDossierRequest extends FormRequest
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
            'type_affaire' => ['required', 'string', 'max:255'],
            'statut' => ['required', 'in:En cours,Gagné,Perdu,Fermé'],
            'client_id' => ['required', 'exists:clients,id'],
            'avocat_id' => ['required', new EstAvocat],
            'date_ouverture' => ['required', 'date'],
            'date_fermeture' => ['nullable', 'date', 'after_or_equal:date_ouverture', 'required_if:statut,Fermé'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type_affaire.required' => 'Le type d\'affaire est requis.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut doit être : En cours, Gagné, Perdu ou Fermé.',
            'client_id.required' => 'Un client doit être sélectionné.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'avocat_id.required' => 'Un avocat doit être sélectionné.',
            'date_ouverture.required' => 'La date d\'ouverture est requise.',
            'date_fermeture.after_or_equal' => 'La date de fermeture doit être postérieure à la date d\'ouverture.',
            'date_fermeture.required_if' => 'La date de fermeture est requise lorsque le dossier est Fermé.',
        ];
    }
}
