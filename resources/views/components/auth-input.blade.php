@props(['type' => 'text', 'name', 'placeholder' => '', 'value' => old($name)])

<input  type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
         'class' => 'w-full rounded-lg border-gray-300 focus:border-[#D6B35A] focus:ring-[#D6B35A]'
    ]) }}>
