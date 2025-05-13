@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    {{-- Main Page Container --}}
    <div class="min-h-screen bg-[#EBF7F4] pt-2 pb-8 text-gray-800">

        {{-- Header --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    {{-- Pagrindinė antraštė  --}}
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Sveiki, {{ Auth::user()->name ?? 'Naudotojau' }}!</h1>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column: Product List  --}}
            <div class="lg:col-span-2">
                {{-- Product List Card --}}
                <div class="bg-white rounded-2xl p-4 shadow flex flex-col hover:shadow-lg transition">
                    {{-- Card Header with Title and Add Button --}}
                    <div class="flex justify-between items-center mb-3 flex-shrink-0">
                        {{-- Bloko antraštė  --}}
                        <h2 class="text-lg font-semibold text-gray-800 opacity-50">
                            Sandėlio likutis
                        </h2>
                        {{-- Mygtukas "Pridėti produktą"  --}}
                        <a href="{{ route('product.create.step1') }}"
                           class="inline-flex items-center px-4 py-2 bg-[#D6B35A] border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-[#c6a34a] active:bg-[#b6933a] focus:outline-none focus:ring-2 focus:ring-[#D6B35A] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Pridėti produktą
                        </a>
                    </div>

                    {{-- Scrollable Product List Container --}}
                    <div class="flex-grow overflow-y-auto space-y-3 max-h-[37rem] pr-2 -mr-2">

                        {{-- Product Loop --}}
                        @forelse ($products as $product)
                            <div class="block group">
                                {{-- Product Item Container --}}
                                <div class="flex items-start justify-between p-3 border border-gray-200 rounded-xl group-hover:bg-gray-50 transition">
                                    {{-- Left side (Image & Text Details) --}}
                                    <div class="flex gap-4 items-start flex-grow mr-2">
                                        {{-- Image (Padidintas) --}}
                                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/64?text=Nėra' }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-md flex-shrink-0 mt-1 border">
                                        {{-- Text Details Container --}}
                                        <div class="flex-grow">
                                            {{-- Name (Link to Edit) --}}
                                            <a href="{{ route('products.edit', $product) }}" class="text-base font-semibold text-gray-900 hover:text-indigo-600 hover:underline">{{ $product->name }}</a>
                                            {{-- Quantity & Expiry --}}
                                            <div class="text-sm text-gray-500 mt-0.5">
                                                {{ $product->quantity }} vnt. – Galioja iki {{ Carbon::parse($product->expiration_date)->isoFormat('YYYY-MM-DD') }}
                                            </div>

                                            {{-- Expiration Status Logic --}}
                                            @php
                                                $expiryDate = Carbon::parse($product->expiration_date)->startOfDay();
                                                $today = Carbon::today();
                                                $tomorrow = Carbon::tomorrow();
                                            @endphp
                                            @if ($expiryDate->isPast())
                                                <div class="mt-1 flex items-center gap-1 text-sm font-medium text-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                    <span>Baigė galioti</span>
                                                </div>
                                            @elseif ($expiryDate->isSameDay($tomorrow))
                                                <div class="mt-1 flex items-center gap-1 text-sm font-medium text-orange-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                                    <span>Rytoj paskutinė galiojimo diena</span>
                                                </div>
                                            @endif

                                            {{-- Metrics Calculation & Display --}}
                                            @php
                                                $pricePerUnit = $product->price ?? 0;
                                                $quantity = $product->quantity ?? 0;
                                                $totalValue = $quantity * $pricePerUnit;
                                                $weightPerUnitKg = 0;
                                                $weightStr = strtolower($product->weight ?? '');
                                                if (!empty($weightStr) && $weightStr !== '0') {
                                                    $weightValue = (float) filter_var($weightStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                                    if (str_contains($weightStr, 'kg')) { $weightPerUnitKg = $weightValue; }
                                                    elseif (str_contains($weightStr, 'g')) { $weightPerUnitKg = $weightValue / 1000; }
                                                    elseif (str_contains($weightStr, 'l')) { $weightPerUnitKg = $weightValue; }
                                                    elseif (str_contains($weightStr, 'ml')) { $weightPerUnitKg = $weightValue / 1000; }
                                                }
                                                $totalWeightKg = $quantity * $weightPerUnitKg;
                                                $showWeightInfo = $weightPerUnitKg > 0 || !empty(trim($product->weight ?? ''));
                                            @endphp
                                            {{-- Metrikų konteineris --}}
                                            <div class="mt-2 pt-2 border-t border-gray-100 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600">
                                                {{-- Price Per Unit --}}
                                                <div class="flex items-center" title="Kaina / vnt.">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1 text-gray-400 flex-shrink-0">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-800">{{ number_format($pricePerUnit, 2, ',', ' ') }} €</span>
                                                </div>
                                                {{-- Total Value --}}
                                                <div class="flex items-center" title="Bendra vertė">
                                                    <span class="text-gray-300 mx-1 hidden sm:inline">|</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 mr-1 text-gray-400 flex-shrink-0">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 018.25 20.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-800">{{ number_format($totalValue, 2, ',', ' ') }} €</span>
                                                </div>
                                                {{-- Total Weight --}}
                                                @if($showWeightInfo)
                                                    <div class="flex items-center" title="Bendras svoris">
                                                        <span class="text-gray-300 mx-1 hidden sm:inline">|</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.036.243H8.754a5.988 5.988 0 01-2.036-.243c-.484-.174-.711-.703-.59-1.202L8.75 5.49m10-0a48.416 48.416 0 01-13.5 0" /> </svg>
                                                        <span class="font-medium text-gray-800">
                                                            @if($weightPerUnitKg > 0)
                                                                {{ number_format($totalWeightKg, ($totalWeightKg < 1 && $totalWeightKg != 0 ? 3 : 2), ',', ' ') }} kg
                                                            @else
                                                                {{ $product->weight }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Right side (Edit Icon Link) --}}
                                    <div class="flex-shrink-0 pl-2">
                                        <a href="{{ route('products.edit', $product) }}" class="text-gray-400 hover:text-indigo-600 p-1" title="Redaguoti">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            {{-- Tuščio sąrašo pranešimas --}}
                            <p class="text-center text-gray-500 py-10 italic text-sm">Jūs dar neturite įkėlę produktų.</p>
                        @endforelse

                    </div>
                    {{-- END: Scrollable Product List Container --}}
                </div>
            </div>

            {{-- === DEŠINĖS KOLONOS PRADŽIA === --}}
            <div class="space-y-6 flex flex-col">

                {{-- 2x2 Tinklelis viršuje --}}
                <div class="grid grid-cols-2 gap-6">

                    {{-- Box 1: Tvarumas --}}
                    <a href="{{ route('sustainability.index') }}" class="block group transition">
                        <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group-hover:shadow-lg transition">
                            <p class="text-4xl lg:text-5xl text-green-600 font-bold mb-1">
                                {{ $sustainabilityPercentage ?? 0 }}%
                            </p>
                            <h2 class="text-sm text-black/50">
                                Tvarumo procentas
                            </h2>
                        </div>
                    </a>

                    {{-- Box 2: Pasiekimai --}}
                    <a href="{{ route('sustainability.index') }}" class="block group transition">
                        <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group-hover:shadow-lg transition">
                            @if (isset($badgePath) && $badgePath)
                                <img src="{{ asset($badgePath) }}" alt="Tvarumo ženklelis" class="h-20 lg:h-24 w-auto max-w-[110px] lg:max-w-[130px] mb-2">
                            @else
                                <div class="h-20 lg:h-24 w-20 lg:w-24 flex items-center justify-center text-gray-300 mb-2" title="Tvarumo ženklelio dar nėra">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 lg:h-14 lg:w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                            <h2 class="text-sm text-black/50">
                                Tvarumo ženkliukas
                            </h2>
                        </div>
                    </a>

                    {{-- Box 3: Šiandienos pajamos --}}
                    <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center hover:shadow-lg transition">
                        <p class="text-3xl lg:text-4xl font-bold text-gray-900 mb-1">
                            {{ number_format($todaysIncome ?? 0, 2, ',', ' ') }}<span class="text-3xl lg:text-4xl align-baseline ml-1">€</span>
                        </p>
                        <h2 class="text-sm text-black/50">
                            Šiandienos pajamos
                        </h2>
                    </div>

                    {{-- Box 4: Rezervuoti vnt. --}}
                    <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center hover:shadow-lg transition">
                        <div class="flex items-center justify-center mb-1">
                            <p class="text-3xl lg:text-4xl font-bold text-gray-900">
                                {{ $reservedQuantity ?? 0 }}<span class="text-3xl lg:text-4xl align-baseline ml-1"></span>
                            </p>
                        </div>
                        <h2 class="text-sm text-black/50">
                            Rezervuoti vnt.
                        </h2>
                    </div>

                </div> {{-- End 2x2 Grid Container --}}

                {{-- Top Product Panel --}}
                <div class="bg-white rounded-2xl shadow p-5 hover:shadow-lg transition">
                    @if ($topProduct)
                        {{-- Flex konteineris: Paveikslėlis kairėje, Tekstas dešinėje --}}
                        <div class="flex items-center gap-5">
                            {{-- Image --}}
                            <div class="flex-shrink-0">
                                {{-- Paveikslėlio dydis w-24 h-24 --}}
                                <img src="{{ $topProduct->image ? asset('storage/'.$topProduct->image) : 'https://via.placeholder.com/96?text=Nėra' }}" {{-- Atnaujintas placeholder dydis --}}
                                     alt="{{ $topProduct->name }}"
                                     class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                            </div>
                            {{-- Text Stack --}}
                            <div class="text-left">
                                {{-- Antraštė integruota čia --}}
                                <p class="text-sm font-semibold text-black/50">Top produktas</p>
                                {{-- Produkto Pavadinimas (16px) --}}
                                <p class="text-base font-bold text-gray-900 mt-1">{{ $topProduct->name }}</p> {{-- Padaryta font-bold --}}
                                {{-- Parduotas Kiekis (14px, black/50) --}}
                                @if($topSoldProductData)
                                    <p class="text-sm text-black/50 mt-1">{{ $topSoldProductData->total_quantity_sold }} vnt.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Tuščia būsena --}}
                         <h2 class="text-base font-semibold text-gray-800 mb-4 text-center">Top produktas</h2>
                        <p class="text-sm text-gray-500 py-6 italic text-center">Dar nėra parduotų produktų.</p>
                    @endif
                </div>

            </div>
            {{-- === DEŠINĖS KOLONOS PABAIGA === --}}

        </div>
    </div>
</x-app-layout>
