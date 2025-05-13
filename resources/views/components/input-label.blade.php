@props(['value', 'required' => false]) {{-- Pridėtas 'required' prop su numatytąja false reikšme --}}

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
    @if ($required) {{-- Jei perduotas required = true --}}
        <span class="text-red-500">*</span> {{-- Pridedame raudoną žvaigždutę --}}
    @endif
</label>
