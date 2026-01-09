@props(['type' => 'info', 'message' => null])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 border border-green-400 text-green-700',
        'error'   => 'bg-red-100 border border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border border-yellow-400 text-yellow-700',
        default   => 'bg-gray-100 border border-gray-400 text-gray-700',
    };
@endphp

@if($message)
    <div class="{{ $classes }} px-4 py-2 rounded mb-3 flex justify-between items-center transition transform duration-300 ease-in-out">
        <span>{{ $message }}</span>
        <button onclick="this.parentElement.style.opacity='0'; this.parentElement.style.transform='translateY(-6px)'; setTimeout(() => this.parentElement.style.display='none', 300);"
                class="font-bold ml-4 hover:opacity-70">✕</button>
    </div>
@endif
