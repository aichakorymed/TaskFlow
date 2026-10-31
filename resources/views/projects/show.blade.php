@extends('layouts.taskflow')

@section('title', $project->name)

@section('content')
    {{-- Carte du projet --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-gray-700 dark:text-gray-300">{{ $project->description ?: 'Aucune description.' }}</p>
                <p class="mt-2 text-xs text-gray-500">Créé le {{ $project->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('projects.edit', $project) }}" class="underline text-gray-700 dark:text-gray-300">Modifier</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Supprimer ce projet et toutes ses tâches ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 underline">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Barre d'outils : filtres + ajout --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm text-gray-700 dark:text-gray-300 space-x-4">
            <a href="{{ route('projects.show', $project) }}"
               class="{{ $filter !== 'overdue' ? 'font-bold underline' : 'underline' }}">Toutes</a>
            <a href="{{ route('projects.show', ['project' => $project, 'filter' => 'overdue']) }}"
               class="{{ $filter === 'overdue' ? 'font-bold underline' : 'underline' }}">En retard</a>
        </div>
        <a href="{{ route('projects.tasks.create', $project) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
            + Ajouter une tâche
        </a>
    </div>

    {{-- Tableau des tâches --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Titre</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Échéance</th>
                    <th class="px-4 py-3">Pièce jointe</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                @forelse ($tasks as $task)
                    @php
                        $badge = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'done' => 'bg-green-100 text-green-800',
                        ][$task->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $task->title }}</td>

                        <td class="px-4 py-3">
                            {{-- Accesseur : statut en français --}}
                            <span class="inline-block px-2 py-0.5 rounded text-xs {{ $badge }}">{{ $task->status_label }}</span>

                            {{-- Formulaire de changement rapide de statut --}}
                            <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}" class="mt-2 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" aria-label="Changer le statut"
                                        class="text-xs border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                    @foreach (\App\Models\Task::STATUS_LABELS as $value => $label)
                                        <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs underline">OK</button>
                            </form>
                        </td>

                        <td class="px-4 py-3">
                            @if ($task->deadline)
                                {{ $task->deadline->format('d/m/Y') }}
                                @if ($task->isOverdue())
                                    <span class="ml-1 inline-block px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">En retard</span>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if ($task->attachment_url)
                                <a href="{{ $task->attachment_url }}" target="_blank" class="underline" download>Télécharger</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="underline">Modifier</a>
                                <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}"
                                      onsubmit="return confirm('Supprimer cette tâche ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 underline">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            {{ $filter === 'overdue' ? 'Aucune tâche en retard.' : 'Aucune tâche pour le moment.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        <a href="{{ route('projects.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">&larr; Retour aux projets</a>
    </div>
@endsection
