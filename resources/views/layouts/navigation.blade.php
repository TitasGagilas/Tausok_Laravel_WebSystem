<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/taupyk_logo.png') }}" alt="Taupyk Maistą Logo" class="block h-10 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index') || request()->routeIs('products.edit')">
                        Produktai
                    </x-nav-link>
                    {{-- START: NEW Navigation Link --}}
                    <x-nav-link :href="route('products.quantity.index')" :active="request()->routeIs('products.quantity.index')">
                        Kiekių Valdymas {{-- Link to the new page --}}
                    </x-nav-link>
                    {{-- END: Navigation Link --}}
                    <x-nav-link :href="route('sustainability.index')" :active="request()->routeIs('sustainability.index')">
                        Statistika
                    </x-nav-link>
                    {{-- +++ Add Info Link +++ --}}
                    <x-nav-link :href="route('info.index')" :active="request()->routeIs('info.index')">
                        Apie
                    </x-nav-link>
                    {{-- +++ End Info Link +++ --}}
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-base leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 2 15 15">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profilis
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Atsijungti') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index') || request()->routeIs('products.edit')">
                Produktai
            </x-responsive-nav-link>
            {{-- START: NEW Responsive Link --}}
            <x-responsive-nav-link :href="route('products.quantity.index')" :active="request()->routeIs('products.quantity.index')">
                 Kiekio Valdymas
            </x-responsive-nav-link>
            {{-- END: NEW Responsive Link --}}
            <x-responsive-nav-link :href="route('sustainability.index')" :active="request()->routeIs('sustainability.index')">
                Statistika
            </x-responsive-nav-link>
            {{-- +++ Add Responsive Info Link +++ --}}
            <x-responsive-nav-link :href="route('info.index')" :active="request()->routeIs('info.index')">
                Info
            </x-responsive-nav-link>
            {{-- +++ End Responsive Info Link +++ --}}
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">Admin</div>
                <div class="font-medium text-base text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profilis
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Atsijungti') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
