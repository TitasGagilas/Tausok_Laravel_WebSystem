@php
    use Carbon\Carbon;

    function sortLink($currentSortBy, $currentSortDir, $columnName, $label) {
        $newSortDir = ($currentSortBy == $columnName && $currentSortDir == 'asc') ? 'desc' : 'asc';
        $url = route('products.index', array_merge(request()->except(['sort_by', 'sort_dir']), ['sort_by' => $columnName, 'sort_dir' => $newSortDir]));
        $icon = '';
        if ($currentSortBy == $columnName) {
            $icon = $currentSortDir == 'asc' ? '↑' : '↓';
        }
        return '<a href="' . $url . '" class="inline-flex items-center group text-xs font-medium text-gray-500 uppercase tracking-wider">' . $label . '<span class="ml-1 text-gray-400 group-hover:text-gray-700">' . $icon . '</span></a>';
    }
@endphp

<x-app-layout>
    <div class="min-h-screen bg-[#EBF7F4] py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-semibold mb-6 text-gray-800">Produktų Sąrašas</h1>

            <form method="GET" action="{{ route('products.index') }}" class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    {{-- Search --}}
                    <div class="flex-grow">
                        <label for="search" class="sr-only">Paieška</label>
                        <input type="text" name="search" id="search" placeholder="Ieškoti pagal pavadinimą..."
                               value="{{ request('search') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-2 text-base"> {{-- Pridėta text-base --}}
                    </div>
                    {{-- Tag Filter --}}
                    <div class="w-full sm:w-auto">
                        <label for="tag_filter" class="sr-only">Filtruoti pagal žymą</label>
                        <select name="tag_filter" id="tag_filter" class="w-full sm:w-auto rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-2 text-base"> {{-- Pridėta text-base --}}
                            <option value="Visi" {{ $tagFilter == 'Visi' ? 'selected' : '' }}>Visi</option>
                            <option value="Populiarus" {{ $tagFilter == 'Populiarus' ? 'selected' : '' }}>Populiarūs</option>
                            <option value="Top Paaukota" {{ $tagFilter == 'Top Paaukota' ? 'selected' : '' }}>Top Paaukoti</option>
                            <option value="Nepopuliarus" {{ $tagFilter == 'Nepopuliarus' ? 'selected' : '' }}>Nepopuliarūs</option>
                            <option value="Nėra Likučio" {{ $tagFilter == 'Nėra Likučio' ? 'selected' : '' }}>Nėra Likučio</option>
                        </select>
                    </div>
                    {{-- Submit Button --}}
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#D6B35A] border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-[#c6a34a] active:bg-[#b6933a] focus:outline-none focus:ring-2 focus:ring-[#D6B35A] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm whitespace-nowrap">
                        FILTRUOTI
                    </button>
                    {{-- Add New Button --}}
                    <a href="{{ route('product.create.step1') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-[#D6B35A] border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-[#c6a34a] active:bg-[#b6933a] focus:outline-none focus:ring-2 focus:ring-[#D6B35A] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -ml-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        PRIDĖTI PRODUKTĄ
                    </a>
                </div>
            </form>

            {{-- Product Table --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1024px]">
                        <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nuotrauka</th>
                            <th class="px-4 py-3 text-left">{!! sortLink($sortBy, $sortDir, 'name', 'Pavadinimas') !!}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Žymos</th>
                            <th class="px-3 py-3 text-center">{!! sortLink($sortBy, $sortDir, 'initial_quantity', 'Pradinis Kiekis') !!}</th>
                            <th class="px-3 py-3 text-center">{!! sortLink($sortBy, $sortDir, 'quantity', 'Likutis Sandėlyje') !!}</th>
                            <th class="px-4 py-3 text-left">{!! sortLink($sortBy, $sortDir, 'weight', 'Svoris') !!}</th>
                            <th class="px-4 py-3 text-right">{!! sortLink($sortBy, $sortDir, 'price', 'Kaina/Vnt. (€)') !!}</th>
                            <th class="px-4 py-3 text-left">{!! sortLink($sortBy, $sortDir, 'expiration_date', 'Galiojimo data') !!}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Veiksmai</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50">
                                {{-- Image --}}
                                <td class="px-4 py-2 whitespace-nowrap align-middle"><img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/40' }}" alt="" class="w-10 h-10 object-cover rounded-md border"></td>
                                {{-- Name --}}
                                <td class="px-4 py-2 whitespace-nowrap text-base font-medium text-gray-900 align-middle">{{ $product->name }}</td>
                                {{-- Žymos --}}
                                <td class="px-4 py-2 whitespace-nowrap text-sm align-middle">
                                    <div class="flex flex-wrap gap-1">
                                        @if(in_array($product->id, $popularProductIds ?? []))
                                            <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-medium" title="Daugiausiai parduota vnt.">Populiarus</span>
                                        @endif
                                        @if(in_array($product->id, $topDonatedProductIds ?? []))
                                            <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-medium" title="Daugiausiai paaukota vnt.">Top Paaukota</span>
                                        @endif
                                        @if(in_array($product->id, $unpopularProductIds ?? []) && !in_array($product->id, $popularProductIds ?? []))
                                            <span class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-medium" title="Neparduota nei vieno vnt.">Nepopuliarus</span>
                                        @endif
                                        @if($product->quantity <= 0)
                                            <span class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-medium">Nėra Likučio</span>
                                        @endif
                                    </div>
                                </td>
                                {{-- Initial Qty --}}
                                <td class="px-3 py-2 whitespace-nowrap text-center text-base text-gray-500 align-middle">{{ $product->initial_quantity }}</td>
                                {{-- Current Qty --}}
                                <td class="px-3 py-2 whitespace-nowrap text-center text-base font-semibold text-gray-900 align-middle">{{ $product->quantity }} </td>
                                {{-- Weight --}}
                                <td class="px-4 py-2 whitespace-nowrap text-base text-gray-500 align-middle">{{ $product->weight }}</td>
                                {{-- Price --}}
                                <td class="px-4 py-2 whitespace-nowrap text-base text-gray-500 text-right align-middle">{{ number_format($product->price, 2, ',', ' ') }} €</td>
                                {{-- Expiry Date --}}
                                <td class="px-4 py-2 whitespace-nowrap text-base text-gray-500 align-middle">
                                    {{ Carbon::parse($product->expiration_date)->isoFormat('YYYY-MM-DD') }}
                                    {{-- Expiry Status --}}
                                    @php
                                        $expiryDate = Carbon::parse($product->expiration_date)->startOfDay();
                                        $today = Carbon::today();
                                        $tomorrow = Carbon::tomorrow();
                                    @endphp
                                    @if ($expiryDate->isPast() && $product->quantity > 0)
                                        <span class="text-red-600 text-sm block">(Baigė galioti)</span>
                                    @elseif ($expiryDate->isSameDay($tomorrow) && $product->quantity > 0)
                                        <span class="text-orange-500 text-sm block">(Galioja iki rytojaus)</span>
                                    @elseif ($expiryDate->isToday() && $product->quantity > 0)
                                        <span class="text-orange-500 text-sm block">(Galioja šiandien)</span>
                                    @endif
                                </td>
                                {{-- Actions --}}
                                <td class="px-4 py-2 whitespace-nowrap text-center text-sm font-medium align-middle">
                                    <div class="flex items-center justify-center space-x-3">
                                        {{-- Edit Metadata Link --}}
                                        <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900" title="Redaguoti Informaciją">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /> </svg>
                                        </a>
                                        {{-- Delete Button/Form --}}
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Ar tikrai norite ištrinti šį produktą?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Ištrinti">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"> <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /> </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500 italic">
                                    Produktų sąrašas tuščias.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination Links --}}
                <div class="mt-4">
                    <div class="text-sm">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            {{-- END: Product Table --}}

        </div> {{-- End max-w --}}
    </div> {{-- End min-h-screen --}}
</x-app-layout>
