<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La propriété du projet est vérifiée par le middleware EnsureUserOwnsProject.
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Task::STATUS_LABELS))],
            'deadline' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,txt,zip'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être : en attente, en cours ou terminée.',
            'deadline.date' => "La date limite n'est pas une date valide.",
            'attachment.max' => 'La pièce jointe ne doit pas dépasser 5 Mo.',
            'attachment.mimes' => "Ce type de fichier n'est pas autorisé.",
        ];
    }
}
