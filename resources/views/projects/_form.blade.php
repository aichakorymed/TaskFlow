{{-- Champs communs aux formulaires de création et de modification d'un projet --}}
@csrf

<div>
    <x-input-label for="name" value="Nom du projet" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                  :value="old('name', $project->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Description (facultative)" />
    <textarea id="description" name="description" rows="4"
              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="flex items-center gap-4">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ $cancelUrl }}" class="text-sm text-gray-600 dark:text-gray-400 underline">Annuler</a>
</div>
