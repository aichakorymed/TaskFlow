{{--
    Composant réutilisable : <x-alert type="success">Message</x-alert>
    Types : success (par défaut), error, warning, info
--}}
@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'bg-green-100 border-green-400 text-green-800',
        'error' => 'bg-red-100 border-red-400 text-red-800',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
        'info' => 'bg-blue-100 border-blue-400 text-blue-800',
    ];
@endphp

<div role="alert" {{ $attributes->merge(['class' => 'border px-4 py-3 rounded-md ' . ($styles[$type] ?? $styles['success'])]) }}>
    {{ $slot }}
</div>
