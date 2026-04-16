<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
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
            'email' => ['required_without:contact', 'nullable', 'string', 'email'],
            'contact' => ['required_without:email', 'nullable', 'string'],
            'indicatif' => ['required_with:contact', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'email.required_without' => 'L\'adresse email est obligatoire si le contact n\'est pas fourni.',
            'contact.required_without' => 'Le contact est obligatoire si l\'email n\'est pas fourni.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }
}
