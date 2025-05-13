<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Atnaujinti slaptažodį') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Įsitikinkite, kad jūsų paskyra naudoja ilgą, atsitiktinį slaptažodį, kad išliktų saugi.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-medium text-sm text-gray-700">{{ __('Dabartinis slaptažodis') }} <span class="text-red-500">*</span></label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block font-medium text-sm text-gray-700">{{ __('Naujas slaptažodis') }} <span class="text-red-500">*</span></label>
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block font-medium text-sm text-gray-700">{{ __('Patvirtinti slaptažodį') }} <span class="text-red-500">*</span></label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            {{-- MODIFIED BUTTON: Added classes --}}
            <x-primary-button class="bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                {{ __('Išsaugoti') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Išsaugota.') }}</p>
            @endif
        </div>
    </form>
</section>
