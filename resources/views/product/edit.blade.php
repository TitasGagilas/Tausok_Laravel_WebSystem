@php use Carbon\Carbon; @endphp
<x-app-layout>
    {{-- Consistent background --}}
    <div class="min-h-screen bg-[#EBF7F4] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-semibold mb-8 text-center text-gray-800">
                Redaguoti produkto informaciją
            </h1>

            {{-- Session Status Messages --}}
            @if (session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm border border-red-200">
                    <p class="font-medium">Prašome pataisyti klaidas:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Product Details Update Form --}}
            <form method="POST"
                  action="{{ route('products.update', $product) }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-6">
                @csrf
                @method('PUT')

                <div class="flex flex-col lg:flex-row gap-6 lg:gap-10 items-start">
                    {{-- Image Preview & Upload --}}
                    <div x-data="{ preview: '{{ $product->image ? asset("storage/$product->image") : '' }}' }" class="flex-shrink-0 w-full lg:w-48 text-center lg:text-left">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1 cursor-pointer">Pakeisti nuotrauką</label>
                        {{-- Image Preview Box --}}
                        <div @click="$refs.imageInput.click()" class="w-40 h-40 mx-auto lg:mx-0 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 transition relative group flex items-center justify-center cursor-pointer overflow-hidden">
                            <template x-if="preview">
                                <img :src="preview" class="object-cover w-full h-full" alt="Product image preview">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center text-gray-400 p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg>
                                    <span class="mt-2 block text-xs">Įkelti nuotrauką</span>
                                </div>
                            </template>
                        </div>
                        {{-- Hidden File Input --}}
                        <input id="image" name="image" type="file" accept="image/*" class="hidden" x-ref="imageInput" @change="preview = URL.createObjectURL($event.target.files[0])">
                        <x-input-error :messages="$errors->get('image')" class="mt-1 text-center lg:text-left" />
                    </div>

                    {{-- Text Fields --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Pavadinimas" />
                            <x-text-input id="name" name="name" value="{{ old('name', $product->name) }}" class="w-full mt-1" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        {{-- Initial Quantity --}}
                        <div>
                            <x-input-label for="initial_quantity" value="Pradinis Kiekis (vnt.)" />
                            <x-text-input id="initial_quantity" name="initial_quantity" type="number" min="0"
                                          value="{{ old('initial_quantity', $product->initial_quantity) }}"
                                          class="w-full mt-1" required />
                            <x-input-error :messages="$errors->get('initial_quantity')" class="mt-1" />
                            <p class="text-xs text-gray-500 mt-1">Pakeitus šį kiekį, automatiškai bus pakoreguotas ir dabartinis likutis sandėlyje.</p>
                        </div>

                        {{-- Price --}}
                        <div>
                            <x-input-label for="price" value="Kaina/vnt. (€)" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="w-full mt-1" required placeholder="pvz. 2.50"/>
                            <x-input-error :messages="$errors->get('price')" class="mt-1" />
                        </div>

                        {{-- Expiry Date --}}
                        <div>
                            <x-input-label for="expiration_date" value="Galiojimo data" />
                            <x-text-input id="expiration_date" name="expiration_date" type="date" value="{{ old('expiration_date', optional($product->expiration_date)->format('Y-m-d')) }}" class="w-full mt-1" required />
                            <x-input-error :messages="$errors->get('expiration_date')" class="mt-1" />
                        </div>

                        {{-- Weight --}}
                        <div>
                            <x-input-label for="weight" value="Svoris/Vnt." />
                            <x-text-input id="weight" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full mt-1" required placeholder="pvz. 500g, 1kg, 2vnt"/>
                            <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <x-input-label for="description" value="Aprašymas" />
                            <textarea id="description"
                                      name="description"
                                      class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                      rows="4"
                            >{{ old('description', $product->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <x-primary-button class="bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                        Išsaugoti Pakeitimus
                    </x-primary-button>
                </div>
            </form>
            {{-- END Main Form --}}

        </div>
    </div>
</x-app-layout>
