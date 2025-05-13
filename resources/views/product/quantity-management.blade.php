@php use Carbon\Carbon; @endphp
<x-app-layout>
    <div class="min-h-screen bg-[#EBF7F4] py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-semibold mb-6 text-gray-800">Produkto Kiekių Valdymas</h1>

            {{-- Session Status Messages --}}
            {{-- ... pranešimai ... --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any() && !$errors->hasBag('product_action'))
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm border border-red-200">
                    <p class="font-medium">Prašome pataisyti klaidas:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif


            <form method="POST" action="{{ route('products.quantity.store') }}">
                @csrf
                {{-- +++ DATOS PASIRINKIMAS (Pakeista) ++++++++++ --}}
                <div class="mb-4 flex justify-start items-center space-x-2">
                    <label for="target_date" class="text-sm font-medium text-gray-700 whitespace-nowrap">Pakeitimų data:</label>
                    <input type="date"
                           id="target_date"
                           name="target_date"
                           value="{{ old('target_date', now()->toDateString()) }}"
                           max="{{ now()->toDateString() }}"
                           class="appearance-none w-auto rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-1 text-sm"
                           required>
                    <div class="w-48">
                       <x-input-error :messages="$errors->get('target_date')" class="mt-1" />
                    </div>
                </div>
                {{-- ++++++++++ DATOS PASIRINKIMO PABAIGA ++++++++++ --}}
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1100px]">
                            <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">Produktas</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pradinis Kiekis</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Likutis Sandėlyje</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-green-700 bg-green-50 uppercase tracking-wider w-28">Parduota</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-blue-700 bg-blue-50 uppercase tracking-wider w-28">Paaukota</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-red-700 bg-red-50 uppercase tracking-wider w-28">Išmesta</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-yellow-700 bg-yellow-50 uppercase tracking-wider w-28">Rezervuota</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24"
                                    title="Tikrinama, ar (Pradinis kiekis - Parduota - Paaukota - Išmesta - Likutis) = 0 IR ar Rezervuota <= Likutis.">
                                    Patikrinimas
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($products as $product)
                                <tr x-data="{
                                         initialQty: {{ $product->initial_quantity }},
                                         sold: {{ $product->sold_quantity ?? 0 }},
                                         donated: {{ $product->donated_quantity ?? 0 }},
                                         wasted: {{ $product->wasted_quantity ?? 0 }},
                                         reserved: {{ $product->reserved_quantity ?? 0 }},
                                         get inShopCalculated() {
                                             const calculated = this.initialQty - this.sold - this.donated - this.wasted;
                                             return Math.max(0, calculated);
                                         },
                                         get balance() {
                                             let outgoing = this.sold + this.donated + this.wasted;
                                             let balanceValue = this.initialQty - outgoing - this.inShopCalculated;
                                             let reservedCheck = this.reserved <= this.inShopCalculated;
                                             return balanceValue === 0 && reservedCheck;
                                         }
                                     }" class="hover:bg-gray-50">
                                    {{-- Product Info --}}
                                    <td class="px-4 py-2 whitespace-nowrap sticky left-0 bg-white group-hover:bg-gray-50 z-10">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-md object-cover border" src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/40?text=N/A' }}" alt="">
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-base font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">Gal. iki: {{ Carbon::parse($product->expiration_date)->isoFormat('YYYY-MM-DD') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Initial Qty --}}
                                    <td class="px-3 py-2 text-center text-base text-gray-500 align-middle" x-text="initialQty"></td>
                                    {{-- Current In Stock Qty (Calculated by JS) --}}
                                    <td class="px-3 py-2 text-center text-base font-bold text-gray-800 align-middle" x-text="inShopCalculated"></td>

                                    {{-- Input Fields per Status --}}
                                    <td class="px-3 py-2 text-center align-middle"><input type="number" name="quantities[{{ $product->id }}][Parduota]" x-model.number="sold" min="0" :max="initialQty" class="w-20 rounded border-gray-300 text-base text-center shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></td>
                                    <td class="px-3 py-2 text-center align-middle"><input type="number" name="quantities[{{ $product->id }}][Paaukota]" x-model.number="donated" min="0" :max="initialQty" class="w-20 rounded border-gray-300 text-base text-center shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></td>
                                    <td class="px-3 py-2 text-center align-middle"><input type="number" name="quantities[{{ $product->id }}][Išmesta]" x-model.number="wasted" min="0" :max="initialQty" class="w-20 rounded border-gray-300 text-base text-center shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></td>
                                    <td class="px-3 py-2 text-center align-middle"><input type="number" name="quantities[{{ $product->id }}][Rezervuota]" x-model.number="reserved" min="0" :max="inShopCalculated" class="w-20 rounded border-gray-300 text-base text-center shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></td>

                                    {{-- Balance Check Indicator --}}
                                    <td class="px-3 py-2 text-center text-base align-middle">
                                        <template x-if="balance">
                                            <span class="text-green-500" title="Balansas teisingas">✓</span>
                                        </template>
                                        <template x-if="!balance">
                                            <span class="text-red-500 font-bold" title="Klaidų balanse!">!</span>
                                        </template>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-sm text-gray-500 italic">
                                        Produktų nerasta. <a href="{{ route('product.create.step1') }}" class="text-indigo-600 hover:underline">Pridėkite naują?</a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Save Button --}}
                    <div class="p-4 bg-gray-50 border-t">
                        <div class="flex justify-end">
                            <x-primary-button class="bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a] text-base font-semibold">
                                Išsaugoti
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Pagination Links --}}
            <div class="mt-4">
                <div class="text-sm">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
