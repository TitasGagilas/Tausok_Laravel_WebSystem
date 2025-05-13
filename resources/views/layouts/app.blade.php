<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-[#EBF7F4]">
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="pt-16">
        {{ $slot }}
    </main>

    {{-- ++++++++++ PORAŠTĖS PRADŽIA ++++++++++ --}}
    <footer class="bg-white border-t border-gray-200 py-5 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Konteineris sulygiavimui --}}
            <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left space-y-3 md:space-y-0">
                {{-- Kairė pusė: Kontaktai --}}
                <div class="text-sm text-gray-600 space-y-1 md:space-y-0 md:space-x-4">
                            <span class="block md:inline">
                                El. paštas: <a href="mailto:klientai@tausok.lt" class="text-indigo-600 hover:underline">klientai@tausok.lt</a>
                            </span>
                    <span class="block md:inline">
                                Tel: <a href="tel:+37067982113" class="text-indigo-600 hover:underline">+370 679 82113</a>
                            </span>
                </div>

                {{-- Dešinė pusė: Autorinės teisės --}}
                <div class="text-sm text-gray-500">
                    &copy; TAUSOK {{ date('Y') }} Visos teisės saugomos.
                </div>
            </div>
        </div>
    </footer>
    {{-- ++++++++++ PORAŠTĖS PABAIGA ++++++++++ --}}

</div>
</body>
</html>
