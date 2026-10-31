<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validation de la création ET de la modification d'un projet. */
class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'accès (connexion + propriété du projet) est géré par les middlewares.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du projet est obligatoire.',
            'name.max' => 'Le nom du projet ne doit pas dépasser 255 caractères.',
        ];
    }
}
