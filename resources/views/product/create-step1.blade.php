<x-app-layout>
    <div class="min-h-screen bg-[#EBF7F4] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('product.store.step1') }}" enctype="multipart/form-data" class="bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-5 w-full max-w-3xl mx-auto">
                @csrf
                <h2 class="text-xl font-bold text-center mb-4 text-gray-800">
                    Pridėti produktą
                </h2>

                {{-- Name Input --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Pavadinimas <span class="text-red-500">*</span></label>
                    {{-- placeholder --}}
                    <x-text-input id="name" name="name" type="text" class="w-full placeholder:opacity-50" :value="old('name')" required placeholder="pvz. Spurga" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Description Input --}}
                <div>
                    {{-- žvaigždutė prie etiketės --}}
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Aprašymas <span class="text-red-500">*</span></label>
                    {{--  placeholder --}}
                    <textarea id="description" name="description" required class="w-full rounded-lg border-gray-300 placeholder:opacity-50 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" rows="3" placeholder="pvz. Švieži spurga su šokoladiniais pabarstukais ir vaniliniu glaistu">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                {{-- Image Upload Area --}}
                <div x-data="{ preview: null }">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Nuotrauka (Pasirinktinai)</label>
                    <label for="image" class="cursor-pointer block">
                        <div class="bg-gray-100 border border-dashed border-gray-300 hover:border-gray-400 h-36 rounded-xl flex items-center justify-center relative overflow-hidden transition group">
                            {{-- Preview Template --}}
                            <template x-if="preview">
                                <img :src="preview" class="object-cover w-full h-full" alt="Image Preview" />
                            </template>
                            {{-- Placeholder Icon Template --}}
                            <template x-if="!preview">
                                <div class="text-center text-gray-500 group-hover:text-gray-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                    <p class="mt-1 text-xs">Įkelti nuotrauką</p>
                                </div>
                            </template>
                        </div>
                    </label>
                    {{-- Hidden File Input --}}
                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="hidden"
                        accept="image/*"
                        @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                    >
                    <x-input-error :messages="$errors->get('image')" class="mt-1" />
                </div>

                {{-- Submit Button --}}
                <div class="pt-2 text-right">
                    <x-primary-button class="bg-[#D6B35A] hover:bg-[#c6a34a] focus:bg-[#c6a34a] active:bg-[#b6933a] focus:ring-[#d6b35a]">
                        Toliau
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
