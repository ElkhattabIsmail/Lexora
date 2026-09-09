<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFactureRequest extends FormRequest
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
            'montant' => ['required', 'numeric', 'min:0'],
            'date_facture' => ['required', 'date'],
            'statut' => ['required', 'in:Payée,Non payée'],
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'dossier_id' => ['nullable', Rule::exists('dossiers', 'id')->where('client_id', $this->input('client_id'))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut pas être négatif.',
            'date_facture.required' => 'La date de facturation est requise.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut doit être : Payée ou Non payée.',
            'client_id.required' => 'Un client doit être sélectionné.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'dossier_id.exists' => 'Le dossier sélectionné n\'existe pas ou n\'appartient pas à ce client.',
        ];
    }
}
