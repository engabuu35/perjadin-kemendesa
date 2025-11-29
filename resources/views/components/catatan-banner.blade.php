@props(['statusClass' => null, 'text' => ''])

@php
    // Jika statusClass diberikan (ex: 'bg-blue-500'), ubah suffix -500/-600/-700 -> -50
    // Aman digunakan di Blade karena ini hanya bekerja pada string kelas.
    $classBg = 'bg-gray-50';
    if ($statusClass) {
        // ganti akhir -500/-600/-700 jadi -50, fallback jika tidak cocok
        $classBg = preg_replace('/-\\d{3}$/', '-50', $statusClass) ?: 'bg-gray-50';
    }

    // Pilih warna teks sesuai background
    $textColor = 'text-gray-700';
    if (str_contains($classBg, 'red')) $textColor = 'text-red-700';
    elseif (str_contains($classBg, 'yellow')) $textColor = 'text-yellow-700';
    elseif (str_contains($classBg, 'green')) $textColor = 'text-green-700';
    elseif (str_contains($classBg, 'blue')) $textColor = 'text-blue-700';
@endphp

<div class="{{ $classBg }} px-6 py-3 border-t border-gray-100">
    <p class="{{ $textColor }} text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-info-circle text-lg"></i>
        <span>{{ $text }}</span>
    </p>
</div>
