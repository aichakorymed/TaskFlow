@extends('layouts.taskflow')

@section('title', 'Nouvelle tâche — ' . $project->name)

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        {{-- enctype obligatoire pour l'envoi de fichier --}}
        <form method="POST" action="{{ route('projects.tasks.store', $project) }}" enctype="multipart/form-data" class="space-y-6">
            @include('tasks._form', ['submitLabel' => 'Ajouter la tâche'])
        </form>
    </div>
@endsection
