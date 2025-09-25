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
            'pieceIdentite' => ' required|mimes:png,jpg,jpeg,pdf|max:1000',
            'extraitMariage' => 'required|mimes:png,jpg,jpeg,pdf|max:1000',
            ];
    }

    public function messages() 
    {
        return [
            'pieceIdentite.required' => 'La CNI est obligatoire.',
            'extraitMariage.required' => 'L\'extrait de mariage obligatoire.',
            'pieceIdentite.mimes' => 'Le format de l\'image doit être PNG, JPG ou JPEG.',
            'pieceIdentite.max' => 'La taille de l\'image ne doit pas dépasser 1000Ko.',
            'extraitMariage.mimes' => 'Le format de l\'image doit être PNG, JPG ou JPEG.',
            'extraitMariage.max' => 'La taille de l\'image ne doit pas dépasser 1000Ko.',
        ];
    }
}
