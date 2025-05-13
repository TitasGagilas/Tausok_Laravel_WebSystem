<x-app-layout>
    <div class="min-h-screen bg-[#EBF7F4] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('product.store.step2') }}" class="bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-5 w-full max-w-3xl mx-auto">
                @csrf
                <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">
                    Pridėti produkto detales
                </h2>

                {{-- Expiration Date --}}
                <div>
                    <label for="expiration_date" class="block font-medium text-sm text-gray-700">Galiojimo data <span class="text-red-500">*</span></label>
                    <x-text-input id="expiration_date" name="expiration_date" type="date" class="w-full mt-1" required :value="old('expiration_date')" />
                    <x-input-error :messages="$errors->get('expiration_date')" class="mt-1" />
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block font-medium text-sm text-gray-700">Kiekis (vnt.) <span class="text-red-500">*</span></label>
                    {{-- placeholder ir opacity klasė --}}
                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="w-full mt-1 placeholder:text-gray-400 placeholder:opacity-50" required :value="old('quantity')" placeholder="pvz. 1" />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                </div>

                {{-- Weight --}}
                <div>
                    {{-- standartinę label žymą --}}
                    <label for="weight" class="block font-medium text-sm text-gray-700">Svoris (g./kg.) <span class="text-red-500">*</span></label>
                    {{-- placeholder --}}
                    <x-text-input id="weight" name="weight" type="text" class="w-full mt-1 placeholder:text-gray-400 placeholder:opacity-50" required :value="old('weight')" placeholder="pvz. 500g, 0,5kg" />
                    <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                </div>

                {{-- Price --}}
                <div>
                    {{-- standartinę label žymą --}}
                    <label for="price" class="block font-medium text-sm text-gray-700">Kaina/vnt. (€) <span class="text-red-500">*</span></label>
                    {{-- placeholder --}}
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="w-full mt-1 placeholder:text-gray-400 placeholder:opacity-50" required :value="old('price')" placeholder="pvz. 2.50" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                {{-- Submit Button --}}
                <div class="pt-3 text-right">
                    <x-primary-button class="bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                        Išsaugoti produktą
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
