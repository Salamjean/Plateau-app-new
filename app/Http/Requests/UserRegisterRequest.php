<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
             'name' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:users,email',
            'commune' => 'required',
            'indicatif' => 'required',
            'contact' => 'required',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&.]/',
            ],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'diaspora' => 'nullable|boolean',
            'pays_residence' => 'required_if:diaspora,1',
            'ville_residence' => 'required_if:diaspora,1',
            'adresse_etrangere' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà associée à un compte.',
            'commune.required' => 'La commune de naissance est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'indicatif.required' => 'L\'indicatif du pays est obligatoire.',
            'contact.required' => 'Le contact est obligatoire.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou svg.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2048 KB.',
            'pays_residence.required_if' => 'Le pays de résidence est obligatoire pour les membres de la diaspora.',
            'ville_residence.required_if' => 'La ville de résidence est obligatoire pour les membres de la diaspora.',
        ];
    }
}
