@extends('layouts.taskflow')

@section('title', 'Modifier la tâche — ' . $project->name)

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}" enctype="multipart/form-data" class="space-y-6">
            @method('PUT')
            @include('tasks._form', ['submitLabel' => 'Enregistrer'])
        </form>
    </div>
@endsection
