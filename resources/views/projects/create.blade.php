@extends('layouts.taskflow')

@section('title', 'Nouveau projet')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
            @include('projects._form', [
                'submitLabel' => 'Créer le projet',
                'cancelUrl' => route('projects.index'),
            ])
        </form>
    </div>
@endsection
