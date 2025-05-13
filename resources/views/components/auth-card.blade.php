@props(['title'])

<div class="min-h-screen flex flex-col items-center justify-center bg-[#DFF4F0] px-4">
    {{-- logo --}}
    <img src="{{ asset('images/logo–taupykmaista.svg') }}"
         alt="Taupyk Maistą" class="w-56 mb-6">

    {{-- white card --}}
    <div {{ $attributes->merge([
            'class' => 'w-full max-w-sm bg-white rounded-3xl shadow p-9'
        ]) }}>
        <h1 class="text-center font-bold text-2xl tracking-widest mb-8 text-gray-900">
            {{ $title }}
        </h1>

        {{ $slot }}
    </div>
</div>
