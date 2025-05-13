@props([
    'icon',
    'iconColor'  => 'text-gray-400',
    'title',
    'value',
    'valueColor' => 'text-gray-800',
])

<div class="bg-white rounded-2xl shadow flex flex-col items-center justify-center p-4 space-y-1">
    {{-- ─ Icon ─ --}}
    <x-dynamic-component
        :component="'heroicon-o-' . $icon"
        class="w-8 h-8 {{ $iconColor }}"
    />

    {{-- ─ Title ─ --}}
    <h2 class="text-gray-500 text-xs font-medium leading-tight">{!! $title !!}</h2>

    {{-- ─ Value ─ --}}
    <p class="text-2xl font-bold {{ $valueColor }}">{{ $value }}</p>
</div>
