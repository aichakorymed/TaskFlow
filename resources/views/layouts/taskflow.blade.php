{{--
    Layout principal de TaskFlow.

    Breeze utilise déjà layouts/app.blade.php comme composant <x-app-layout>
    (utilisé par le profil, le dashboard...). On ne le remplace donc pas :
    ce layout "étend" la base de Breeze en l'enveloppant, et expose
    @yield('title') et @yield('content') pour les vues enfants :

        @extends('layouts.taskflow')
        @section('title', 'Mon titre')
        @section('content') ... @endsection
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @yield('title', 'TaskFlow')
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Messages flash et erreurs de validation, via le composant réutilisable --}}
            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error">
                    <p class="font-semibold">Le formulaire contient des erreurs :</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @yield('content')
        </div>
    </div>
</x-app-layout>
