@props(['statusClass' => 'bg-gray-500', 'status' => '—'])

@php
    // Tentukan icon berdasarkan warna di kelas tailwind
    $icon = 'fa-circle';
    if (str_contains($statusClass, 'red')) $icon = 'fa-circle-exclamation';
    elseif (str_contains($statusClass, 'yellow')) $icon = 'fa-spinner animate-pulse';
    elseif (str_contains($statusClass, 'green')) $icon = 'fa-circle-check';
    elseif (str_contains($statusClass, 'blue')) $icon = 'fa-circle';
@endphp

<span class="px-4 py-2 text-sm font-bold text-white rounded-full shadow-md {{ $statusClass }}
    flex items-center gap-2 hover:brightness-110 hover:scale-105 transition-all duration-200 whitespace-nowrap">
    <i class="fa-solid {{ $icon }} text-xs"></i>
    {{ $status }}
</span>
