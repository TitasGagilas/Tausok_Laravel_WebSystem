<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Form Title --}}
    <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">
        Prisijungti
    </h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('El. paštas')" :required="true" />
            <x-text-input id="email" class="block mt-1 w-full placeholder:text-gray-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="paštas@restoranas.lt" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Slaptažodis')" :required="true" />
            <x-text-input id="password" class="block mt-1 w-full placeholder:text-gray-400"
                          type="password"
                          name="password"
                          required autocomplete="current-password" placeholder="slaptažodis123" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#D6B35A] shadow-sm focus:ring-[#D6B35A]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Prisiminti mane') }}</span>
            </label>
        </div>

        {{-- Login Button --}}
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-[16px] bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                {{ __('Prisijungti') }}
            </x-primary-button>
        </div>

        <div class="mt-8 text-center">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D6B35A]" href="{{ route('password.request') }}">
                    {{ __('Pamiršote slaptažodį?') }}
                </a>
            @endif
        </div>

        {{-- Register Link --}}
        <div class="mt-6 text-center text-sm">
            Neturite paskyros?
            <a class="underline text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                Registruotis
            </a>
        </div>
    </form>
</x-guest-layout>
