<div class="mb-1">
    <h2 class="text-gray-700 text-3xl font-bold pb-2 relative leading-tight">
        {{ $title }}
        <span class="absolute bottom-0 left-0 w-64 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
    </h2>

    @if(!empty($subtitle))
        <p class="text-gray-700 text-md mt-2">
            {{ $subtitle }}
        </p>
    @endif
</div>
