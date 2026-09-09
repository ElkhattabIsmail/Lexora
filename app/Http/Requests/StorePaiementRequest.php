<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaiementRequest extends FormRequest
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
            'montant' => ['required', 'numeric', 'gt:0'],
            'date_paiement' => ['required', 'date'],
            'mode_paiement' => ['required', 'in:Virement bancaire,Chèque,Espèces,Carte bancaire'],
            'reference' => ['nullable', 'string', 'max:255'],
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
            'montant.gt' => 'Le montant doit être supérieur à zéro.',
            'date_paiement.required' => 'La date du paiement est requise.',
            'mode_paiement.required' => 'Le mode de paiement est requis.',
            'mode_paiement.in' => 'Le mode de paiement choisi n\'est pas valide.',
        ];
    }

    /**
     * Empêche un paiement de dépasser le montant restant dû de la facture.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $facture = $this->route('facture');

            if (! $facture) {
                return;
            }

            $montant = (float) $this->input('montant');
            $montantRestant = (float) $facture->montant_restant;

            if ($montant > $montantRestant) {
                $validator->errors()->add(
                    'montant',
                    'Le montant payé ne peut pas dépasser le montant restant dû ('.number_format($montantRestant, 2, ',', ' ').' €).',
                );
            }
        });
    }
}
