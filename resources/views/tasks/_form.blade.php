{{-- Champs communs aux formulaires de création et de modification d'une tâche --}}
@csrf

<div>
    <x-input-label for="title" value="Titre" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                  :value="old('title', $task->title)" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="status" value="Statut" />
    <select id="status" name="status" required
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (\App\Models\Task::STATUS_LABELS as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div>
    <x-input-label for="deadline" value="Date limite (facultative)" />
    <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full"
                  :value="old('deadline', $task->deadline?->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
</div>

<div>
    <x-input-label for="attachment" value="Pièce jointe (facultative, 5 Mo max)" />
    <input id="attachment" name="attachment" type="file"
           class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300" />
    @if ($task->attachment_url)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Fichier actuel :
            <a href="{{ $task->attachment_url }}" target="_blank" class="underline">{{ basename($task->attachment_path) }}</a>
            (en choisir un nouveau le remplacera)
        </p>
    @endif
    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
</div>

<div class="flex items-center gap-4">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-600 dark:text-gray-400 underline">Annuler</a>
</div>
