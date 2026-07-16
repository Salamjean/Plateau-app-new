<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class saveMariageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pieceIdentite' => 'required|mimes:png,jpg,jpeg,pdf|max:25600',
            'extraitMariage' => 'nullable|mimes:png,jpg,jpeg,pdf|max:25600',
            'commune_mariage' => 'required|string|max:255',
            'pour' => 'nullable|string',
            'relation' => 'nullable|string',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|mimes:png,jpg,jpeg,pdf|max:25600',
            'dateNaissanceEpoux' => 'nullable|date|before:-18 years',
            'dateNaissanceEpouse' => 'nullable|date|before:-18 years',
            'numero_registre' => 'required|string|max:255',
            'date_registre' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'pieceIdentite.required' => 'La pièce d\'identité est obligatoire.',
            'commune_mariage.required' => 'La commune de mariage est obligatoire.',
            'numero_registre.required' => 'Le numéro de registre est obligatoire.',
            'date_registre.required' => 'La date de registre est obligatoire.',
            'date_registre.date' => 'La date de registre n\'est pas valide.',
            'pieceIdentite.mimes' => 'Le format de la pièce d\'identité doit être PNG, JPG, JPEG ou PDF.',
            'pieceIdentite.max' => 'La taille de la pièce d\'identité ne doit pas dépasser 25Mo.',
            'document_autorisation.required_if' => 'Le document d\'autorisation est obligatoire pour une connaissance.',
            'extraitMariage.mimes' => 'Le format de l\'ancien acte doit être PNG, JPG, JPEG ou PDF.',
            'extraitMariage.max' => 'La taille de l\'ancien acte ne doit pas dépasser 25Mo.',
            'dateNaissanceEpoux.before' => 'Le conjoint doit avoir au moins 18 ans.',
            'dateNaissanceEpouse.before' => 'Le conjoint doit avoir au moins 18 ans.',
        ];
    }
}
