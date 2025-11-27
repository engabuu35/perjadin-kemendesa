@php
    $badge = match($statusColor) {
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'green' => 'bg-green-600',
        'blue' => 'bg-blue-500',
        default => 'bg-gray-500'
    };

    $icon = match($statusColor) {
        'red' => 'fa-circle-exclamation',
        'yellow' => 'fa-spinner animate-pulse',
        'green' => 'fa-circle-check',
        'blue' => 'fa-circle',
        default => 'fa-circle'
    };
@endphp

<span class="px-4 py-2 text-sm font-bold text-white rounded-full shadow-md {{ $badge }}
    flex items-center gap-2 hover:brightness-110 hover:scale-105 transition-all duration-200 whitespace-nowrap">
    
    <i class="fa-solid {{ $icon }} text-xs"></i>
    {{ $status }}
</span>
