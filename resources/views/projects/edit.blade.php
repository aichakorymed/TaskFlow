@extends('layouts.taskflow')

@section('title', 'Modifier le projet')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
            @method('PUT')
            @include('projects._form', [
                'submitLabel' => 'Enregistrer',
                'cancelUrl' => route('projects.show', $project),
            ])
        </form>
    </div>
@endsection
