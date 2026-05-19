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
        ];
    }

    public function messages()
    {
        return [
            'pieceIdentite.required' => 'La pièce d\'identité est obligatoire.',
            'commune_mariage.required' => 'La commune de mariage est obligatoire.',
            'pieceIdentite.mimes' => 'Le format de la pièce d\'identité doit être PNG, JPG, JPEG ou PDF.',
            'pieceIdentite.max' => 'La taille de la pièce d\'identité ne doit pas dépasser 25Mo.',
            'extraitMariage.mimes' => 'Le format de l\'ancien acte doit être PNG, JPG, JPEG ou PDF.',
            'extraitMariage.max' => 'La taille de l\'ancien acte ne doit pas dépasser 25Mo.',
        ];
    }
}
