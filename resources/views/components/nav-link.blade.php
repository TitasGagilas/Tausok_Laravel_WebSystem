@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#D6B35A] text-base font-medium leading-5 text-gray-900 focus:outline-none focus:border-[#c6a34a] transition duration-150 ease-in-out' // Pakeista į geltoną (#D6B35A ir tamsesnę #c6a34a focusui)
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-base font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
