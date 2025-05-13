<x-app-layout>

    <div class="min-h-screen bg-[#EBF7F4] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 md:p-8 rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Informacija apie TAUSOK</h1>

            {{-- Section: About the System --}}
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">TAUSOK privalumai</h2>
                <p class="text-gray-600 leading-relaxed">
                    Ši sistema skirta padėti Jūsų kepyklėlei ar restoranui efektyviai sekti produktų likučius, mažinti maisto švaistymą ir didinti tvarumą. Stebėdami parduotus, paaukotus ir išmestus produktus, galite geriau planuoti gamybą, optimizuoti užsakymus ir prisidėti prie aplinkos tausojimo. Sistema taip pat padeda vizualizuoti Jūsų pastangas per tvarumo rodiklius ir pasiekimų ženklelius.
                </p>
            </section>

            {{-- Section: Sustainability Percentage --}}
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Kaip skaičiuojamas tvarumo procentas?</h2>
                <p class="text-gray-600 leading-relaxed mb-2">
                    Tvarumo procentas parodo, kokia dalis Jūsų pradinių produktų (pagal vienetus) nebuvo išmesta. Jis apskaičiuojamas pagal formulę:
                </p>
                <div class="bg-gray-100 p-3 rounded text-center text-sm text-gray-700 font-mono">
                    ( (Pradinis Produktų Kiekis - Išmestas Produktų Kiekis) / Pradinis Produktų Kiekis ) * 100%
                </div>
                <p class="text-xs text-gray-500 mt-1">Pastaba: Jei pradinis kiekis yra 0, procentas skaičiuojamas specialiu būdu (0% jei buvo transakcijų, 100% jei nebuvo).</p>
            </section>

            {{-- Section: Badge System --}}
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Kaip Gauti Pasiekimų Ženklelį?</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Sistema automatiškai įvertina Jūsų veiklą ir gali apdovanoti pasiekimų ženkleliais. Vertinami šie rodikliai: bendras parduotų ir paaukotų produktų kiekis (vnt.), atskirai parduotų ir paaukotų produktų kiekiai (vnt.) bei išmesto kiekio santykis su parduotu/paaukotu kiekiu (švaistymo santykis).
                </p>
                <div class="space-y-5">
                    {{-- NoBadge --}}
                    <div class="flex items-center gap-4 p-3 border rounded-lg">
                        <img src="{{ asset('badges/nobadge.png') }}" alt="Pradedančiojo ženklelis" class="h-16 w-16 flex-shrink-0">
                        <div>
                            <h3 class="font-semibold text-gray-800">Dar neturite ženklelio</h3>
                            <p class="text-sm text-gray-600">Mažiau nei 150 vnt. parduota ar paaukota.</p>
                        </div>
                    </div>
                    {{-- Bronze --}}
                    <div class="flex items-center gap-4 p-3 border rounded-lg">
                        <img src="{{ asset('badges/bronze.png') }}" alt="Bronzinis ženklelis" class="h-16 w-16 flex-shrink-0">
                        <div>
                            <h3 class="font-semibold text-gray-800">Bronzinis Tausotojas</h3>
                            <ul class="text-sm text-gray-600 list-disc list-inside">
                                <li>Bent 150 vnt. parduota/paaukota</li>
                                <li>Bent 100 vnt. parduota</li>
                                <li>Bent 50 vnt. paaukota</li>
                                <li>Švaistymo santykis* neviršija 40%</li>
                            </ul>
                        </div>
                    </div>
                    {{-- Metal --}}
                    <div class="flex items-center gap-4 p-3 border rounded-lg">
                        <img src="{{ asset('badges/metal.png') }}" alt="Sidabrinis ženklelis" class="h-16 w-16 flex-shrink-0">
                        <div>
                            <h3 class="font-semibold text-gray-800">Sidabrinis Tausotojas</h3>
                            <ul class="text-sm text-gray-600 list-disc list-inside">
                                <li>Bent 800 vnt. parduota/paaukota</li>
                                <li>Bent 500 vnt. parduota</li>
                                <li>Bent 300 vnt. paaukota</li>
                                <li>Švaistymo santykis* neviršija 25%</li>
                            </ul>
                        </div>
                    </div>
                    {{-- Gold --}}
                    <div class="flex items-center gap-4 p-3 border rounded-lg">
                        <img src="{{ asset('badges/gold.png') }}" alt="Auksinis ženklelis" class="h-16 w-16 flex-shrink-0">
                        <div>
                            <h3 class="font-semibold text-gray-800">Auksinis Tausotojas</h3>
                            <ul class="text-sm text-gray-600 list-disc list-inside">
                                <li>Bent 10000 vnt. parduota/paaukota</li>
                                <li>Bent 5000 vnt. parduota</li>
                                <li>Bent 5000 vnt. paaukota</li>
                                <li>Švaistymo santykis* neviršija 10%</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">*Švaistymo santykis = Išmestas Kiekis / (Parduotas Kiekis + Paaukotas Kiekis)</p>
            </section>


            </div>
        </div>
    </div>
</x-app-layout>
