@props(['label'])

<div {{ $attributes->merge(['class'=>'space-y-1']) }}>
    <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
    {{ $slot }}
</div>
