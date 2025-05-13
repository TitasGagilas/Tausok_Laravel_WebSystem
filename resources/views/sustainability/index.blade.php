<x-app-layout>
    <div class="min-h-screen bg-[#EBF7F4] py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold mb-4 text-gray-800">Statistika</h1>
            <p class="text-[16px] text-gray-500 mb-6">
                {{ \Carbon\Carbon::now()->isoFormat('YYYY-MM-DD') }}
            </p>

            {{-- Pirma eilutė  --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                {{-- Box 1: Tvarumo procentas --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group transition hover:shadow-lg">
                    {{-- Reikšmė Viršuje --}}
                    <p class="text-3xl text-green-600 font-bold mb-1.5">
                        {{ $sustainabilityPercentage ?? 0 }}%
                    </p>
                    {{-- Apačioje: Ikona + Etiketė --}}
                    <div class="flex items-center justify-center gap-1.5">
                        {{-- Ikona (Home)  --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        {{-- Etiketė --}}
                        <span class="text-sm text-black/50">Tvarumo procentas</span>
                    </div>
                </div>

                {{-- Box 2: Išmesti produktai --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group transition hover:shadow-lg">
                     {{-- Reikšmė Viršuje --}}
                    <p class="text-3xl text-gray-900 font-bold mb-1.5">
                        {{ round($wasted ?? 0, 2) }}kg
                    </p>
                     {{-- Apačioje: Ikona + Etiketė --}}
                    <div class="flex items-center justify-center gap-1.5">
                         {{-- Ikona (Triangle Exclamation) - Sumažinta --}}
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                         </svg>
                         {{-- Etiketė --}}
                        <span class="text-sm text-black/50">Išmesti produktai</span>
                    </div>
                </div>

                {{-- Box 3: Parduoti produktai --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group transition hover:shadow-lg">
                     {{-- Reikšmė Viršuje --}}
                    <p class="text-3xl text-gray-900 font-bold mb-1.5">
                         {{ $sold ?? 0 }}
                    </p>
                     {{-- Apačioje: Ikona + Etiketė --}}
                    <div class="flex items-center justify-center gap-1.5">
                         {{-- Ikona (Shopping Bag) - Sumažinta --}}
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                         </svg>
                         {{-- Etiketė --}}
                        <span class="text-sm text-black/50">Parduoti produktai</span>
                    </div>
                </div>

                {{-- Box 4: Išsaugota CO2 --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center group transition hover:shadow-lg">
                    {{-- Reikšmė Viršuje --}}
                    <p class="text-3xl text-gray-900 font-bold mb-1.5">
                         {{ round($savedCO2 ?? 0) }}kg
                    </p>
                      {{-- Apačioje: Ikona + Etiketė --}}
                     <div class="flex items-center justify-center gap-1.5">
                         {{-- Ikona (Globe) - Sumažinta --}}
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-green-500 flex-shrink-0"> {{-- Palikta žalia spalva ir h-4 w-4 --}}
                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253M3 14.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z" />
                         </svg>
                         {{-- Etiketė --}}
                        <span class="text-sm text-black/50">Išsaugota CO<sub>2</sub></span>
                    </div>
                </div>

            </div> {{-- Pirmos eilutės pabaiga --}}

            {{-- Antra eilutė (Centruotas turinys) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                {{-- Box 1: Šiandienos pajamos (Centruotas) --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center transition hover:shadow-lg">
                    {{-- Viršutinė dalis: Pagrindinė reikšmė ir Etiketė+Ikona --}}
                    <div class="text-center mb-3">
                        <p class="text-3xl font-bold text-gray-900 mb-1.5">
                            {{ number_format($todaysIncome ?? 0, 2, ',', ' ') }}<span class="text-2xl align-baseline ml-1">€</span>
                        </p>
                        <div class="flex items-center justify-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 100 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm text-black/50">Šiandienos pajamos</span>
                        </div>
                    </div>
                    {{-- Apatinė dalis: Papildoma informacija --}}
                    <div class="pt-2 border-t border-gray-100 w-full">
                        <p class="text-xs text-gray-500">Savaitės pajamos</p>
                        <p class="text-base font-semibold text-gray-700">
                            {{ number_format($weeklyIncome ?? 0, 2, ',', ' ') }}<span class="text-sm align-baseline ml-0.5">€</span>
                        </p>
                    </div>
                </div>

                {{-- Box 2: Rezervuoti vnt. (Centruotas) --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center transition hover:shadow-lg">
                     {{-- Viršutinė dalis: Pagrindinė reikšmė ir Etiketė+Ikona --}}
                     <div class="text-center mb-3">
                        <p class="text-3xl font-bold text-gray-900 mb-1.5">
                            {{ $reservedQuantity ?? 0 }}<span class="text-2xl align-baseline ml-1"></span>
                        </p>
                        <div class="flex items-center justify-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m8.25-8.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-7.5A2.25 2.25 0 018.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h6a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25z" /></svg>
                            <span class="text-sm text-black/50">Rezervuoti vnt.</span>
                        </div>
                    </div>
                     {{-- Apatinė dalis: Papildoma informacija --}}
                    <div class="pt-2 border-t border-gray-100 w-full">
                        <p class="text-xs text-gray-500">Sandėlyje iš viso produktų</p>
                        <p class="text-base font-semibold text-gray-700">
                            {{ $totalStockQuantity ?? 0 }}<span class="text-sm align-baseline ml-0.5">vnt.</span>
                        </p>
                    </div>
                </div>

                {{-- Box 3: Top Produktas --}}
                <div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition flex items-center">
                    @if ($topProduct)
                        <div class="flex items-center gap-4 w-full">
                            <div class="flex-shrink-0">
                                <img src="{{ $topProduct->image ? asset('storage/'.$topProduct->image) : 'https://via.placeholder.com/80?text=Nėra' }}" alt="{{ $topProduct->name }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            </div>
                            <div class="text-left flex-grow">
                                <p class="text-sm font-semibold text-gray-500">Top produktas</p>
                                <p class="text-base font-bold text-gray-900 mt-0.5">{{ $topProduct->name }}</p>
                                @if($topSoldProductData)
                                    <p class="text-sm text-black/50 mt-0.5">{{ $topSoldProductData->total_quantity_sold }} vnt.</p>
                                @endif
                            </div>
                        </div>
                    @else
                         <div class="text-center w-full">
                             <p class="text-sm font-semibold text-gray-500 mb-2">Top produktas</p>
                             <p class="text-sm text-gray-500 py-6 italic">Dar nėra parduotų produktų.</p>
                         </div>
                    @endif
                </div>

                {{-- Box 4: Pasiekimai --}}
                <div class="bg-white rounded-xl shadow p-5 h-full flex flex-col items-center justify-center text-center transition hover:shadow-lg">
                     @if ($badgePath)
                        <img src="{{ asset($badgePath) }}" alt="Tvarumo ženklelis" class="h-16 lg:h-24 w-auto max-w-[90px] lg:max-w-[110px] mb-2">
                    @else
                        <div class="h-16 lg:h-20 w-16 lg:w-20 flex items-center justify-center text-gray-300 mb-2" title="Tvarumo ženklelio dar nėra">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 lg:h-12 lg:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /> </svg>
                        </div>
                    @endif
                    <h2 class="text-sm text-black/50">
                        Tvarumo ženkliukas
                    </h2>
                </div>
            </div>
            {{-- +++ END: Antros eilutės pabaiga +++ --}}

            {{-- CHARTS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-chart-card id="weeklyChart" title="Savaitės pardavimų apžvalga"/>
                <x-chart-card id="leastSoldChart" title="Dažniausiai išmetami produktai"/>
            </div>

        {{-- ++++++++++ TRANSAKCIJŲ ISTORIJOS BLOKAS ++++++++++ --}}
        <div class="mt-8 bg-white p-4 md:p-6 rounded-2xl shadow-lg">
            <h3 class="text-base font-semibold mb-4 text-gray-700">
                Paskutinės Transakcijos
            </h3>
            <div class="max-h-64 overflow-y-auto pr-2 -mr-2 space-y-4">
                @forelse ($transactionHistory as $transaction)
                    <div class="text-sm pb-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-800 flex items-center gap-1.5">
                                @php
                                    $iconClass = 'h-4 w-4 flex-shrink-0';
                                    $iconColor = 'text-gray-400';
                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />';
                                    if($transaction->action_type == 'Parduota') {
                                        $iconColor = 'text-blue-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />';
                                    } elseif($transaction->action_type == 'Išmesta') {
                                        $iconColor = 'text-red-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />';
                                    } elseif($transaction->action_type == 'Paaukota') {
                                        $iconColor = 'text-green-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />';
                                    } elseif($transaction->action_type == 'Rezervuota') {
                                        $iconColor = 'text-orange-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />';
                                    }
                                @endphp
                                <svg class="{{ $iconClass }} {{ $iconColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    {!! $iconPath !!}
                                </svg>
                                <span>{{ $transaction->action_type }}</span>
                            </span>
                            <span class="text-xs text-gray-500 flex-shrink-0 ml-2 whitespace-nowrap">
                                {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->isoFormat('YYYY-MM-DD') : 'N/A' }}
                            </span>
                        </div>
                        <div class="mt-1 text-gray-600 pl-7">
                            <span class="italic">{{ $transaction->product->name ?? 'Nežinomas produktas' }}</span>:
                            <span class="font-medium {{ $transaction->quantity >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $transaction->quantity >= 0 ? '+' : '' }}{{ $transaction->quantity }} vnt.
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic text-center py-4">Transakcijų istorija tuščia.</p>
                @endforelse
            </div>
        </div>
        {{-- ++++++++++ TRANSAKCIJŲ ISTORIJOS BLOKO PABAIGA ++++++++++ --}}
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const weeklyData = @json($weeklySales ?? []);
            const leastSoldData = @json($leastSold ?? []);

            const integerTicks = {
                callback: value => Number.isInteger(value) ? value : ''
            };

            const weeklyChartCanvas = document.getElementById('weeklyChart');
            if (weeklyChartCanvas && Object.keys(weeklyData).length) {
                new Chart(weeklyChartCanvas, {
                    type: 'line',
                    data: {
                        labels: Object.keys(weeklyData),
                        datasets: [{
                            data: Object.values(weeklyData),
                            borderColor: '#D6B35A',
                            backgroundColor: '#D6B35A',
                            tension: .4,
                            pointBackgroundColor: '#D6B35A',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#6b7280', font: { size: 12 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e5e7eb', drawBorder: false },
                                ticks: {
                                    ...integerTicks,
                                    color: '#6b7280',
                                    padding: 8,
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }

            const leastSoldChartCanvas = document.getElementById('leastSoldChart');
            if (leastSoldChartCanvas && Object.keys(leastSoldData).length) {
                new Chart(leastSoldChartCanvas, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(leastSoldData),
                        datasets: [{
                            data: Object.values(leastSoldData),
                            backgroundColor: '#EBCB80',
                            borderColor: '#D6B35A',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                grid: { display: false },
                                ticks: { color: '#6b7280', font: { size: 12 } }
                            },
                            x: {
                                beginAtZero: true,
                                grid: { color: '#e5e7eb', drawBorder: false },
                                ticks: {
                                    ...integerTicks,
                                    color: '#6b7280',
                                    padding: 5,
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

</x-app-layout>
