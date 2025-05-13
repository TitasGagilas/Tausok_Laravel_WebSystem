<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Ačiū, kad užsiregistravote! Prieš pradėdami registraciją, gal galėtumėte patvirtinti savo el. pašto adresą spustelėdami nuorodą, kurią ką tik atsiuntėme el. paštu? Jei jos negavote, mielai atsiųsime kitą.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Registracijos metu nurodytu el. pašto adresu buvo išsiųsta nauja patvirtinimo nuoroda.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
