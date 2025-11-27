<div class="mb-6">
    <h2 class="text-gray-700 text-4xl font-bold pb-3 relative leading-tight">
        {{ $title }}
        <span class="absolute bottom-0 left-0 w-48 h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></span>
    </h2>

    @if(!empty($subtitle))
        <p class="text-gray-700 text-xl mt-4">
            {{ $subtitle }}
        </p>
    @endif
</div>
