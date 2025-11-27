@php
    $background = match($color) {
        'red' => 'bg-red-50',
        'yellow' => 'bg-yellow-50',
        'green' => 'bg-green-50',
        'blue' => 'bg-blue-50',
        default => 'bg-gray-50'
    };

    $textColor = match($color) {
        'red' => 'text-red-700',
        'yellow' => 'text-yellow-700',
        'green' => 'text-green-700',
        'blue' => 'text-blue-700',
        default => 'text-gray-700'
    };

    $icon = match($color) {
        'red' => 'fa-circle-exclamation',
        'yellow' => 'fa-spinner fa-spin',
        'green' => 'fa-circle-check',
        'blue' => 'fa-info-circle',
        default => 'fa-info-circle'
    };
@endphp

<div class="{{ $background }} px-6 py-3 border-t border-gray-100">
    <p class="{{ $textColor }} text-sm font-medium flex items-center gap-2">
        <i class="fa-solid {{ $icon }} text-lg"></i>
        <span>{{ $text }}</span>
    </p>
</div>
