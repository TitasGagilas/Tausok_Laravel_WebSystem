<x-guest-layout>
    <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">
        Registracija
    </h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Vardas')" required />
            <x-text-input
                id="name"
                class="block mt-1 w-full placeholder:text-gray-400"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Tausok Kepyklėlė"
             />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('El. paštas')" required />
            <x-text-input
                id="email"
                class="block mt-1 w-full placeholder:text-gray-400"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                placeholder="paštas@restoranas.lt"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Slaptažodis')" required />
            <x-text-input
                id="password"
                class="block mt-1 w-full placeholder:text-gray-400"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="slaptažodis123"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Patvirtinkite slaptažodį')" required />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full placeholder:text-gray-400"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="slaptažodis123"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Submit Button --}}
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-[16px] bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                {{ __('Registruotis') }}
            </x-primary-button>
        </div>

        {{-- Login Link --}}
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D6B35A]" href="{{ route('login') }}">
                {{ __('Jau turite paskyrą?') }}
            </a>
        </div>
    </form>
</x-guest-layout>
