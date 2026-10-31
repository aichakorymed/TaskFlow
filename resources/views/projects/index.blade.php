@extends('layouts.taskflow')

@section('title', 'Mes projets')

@section('content')
    <div class="flex justify-end">
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
            + Nouveau projet
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
        @if ($projects->isEmpty())
            <p class="p-6 text-gray-600 dark:text-gray-300">Vous n'avez encore aucun projet. Créez-en un pour commencer.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Projet</th>
                        <th class="px-4 py-3">Tâches</th>
                        <th class="px-4 py-3">Terminées</th>
                        <th class="px-4 py-3">En retard</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    @foreach ($projects as $project)
                        {{-- Les tâches sont déjà chargées (with('tasks')) : aucune requête SQL ici --}}
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('projects.show', $project) }}" class="font-semibold underline">{{ $project->name }}</a>
                                @if ($project->description)
                                    <div class="text-xs text-gray-500">{{ str($project->description)->limit(80) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $project->tasks->count() }}</td>
                            <td class="px-4 py-3">{{ $project->tasks->where('status', 'done')->count() }}</td>
                            <td class="px-4 py-3">{{ $project->tasks->filter(fn ($task) => $task->isOverdue())->count() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('projects.show', $project) }}" class="underline">Voir</a>
                                    <a href="{{ route('projects.edit', $project) }}" class="underline">Modifier</a>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                          onsubmit="return confirm('Supprimer ce projet et toutes ses tâches ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 underline">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div>{{ $projects->links() }}</div>
@endsection
