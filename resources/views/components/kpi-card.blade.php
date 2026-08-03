@props(['label', 'value', 'color' => 'gray'])

@php
$colors = [
    'blue' => 'text-blue-600',
    'green' => 'text-green-600',
    'amber' => 'text-amber-600',
    'red' => 'text-red-600',
    'gray' => 'text-gray-800',
];
$textColor = $colors[$color] ?? $colors['gray'];
@endphp

<div class="bg-white rounded-xl shadow p-4">
    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $label }}</p>
    <p class="text-lg sm:text-2xl font-bold {{ $textColor }}">{{ $value }}</p>
</div>
