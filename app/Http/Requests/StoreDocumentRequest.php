<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'fichier' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
            'nom' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:Contrat,Plaidoirie,Jugement,Preuve,Autre'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fichier.required' => 'Un fichier est requis.',
            'fichier.file' => 'Le fichier n\'est pas valide.',
            'fichier.mimes' => 'Le fichier doit être au format PDF, DOC, DOCX, JPG ou PNG.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
        ];
    }
}
