<?php

namespace App\Http\Controllers;

use App\Models\Product;             // Produkto modelis
use App\Models\ProductChange;       // Modelis produktų pakeitimų istorijai
use App\Models\ProductTransaction;  // Modelis produktų kiekių transakcijoms
use Illuminate\Http\Request;        // HTTP užklausos objektas
use Illuminate\Support\Facades\Auth; // Autentifikacijos fasadas
use Illuminate\Support\Facades\DB;   // Duomenų bazės fasadas
use Illuminate\Support\Facades\Log;  // Log'inimo fasadas klaidų registravimui
use Illuminate\Support\Facades\Session; // Sesijos fasadas darbui su sesijos duomenimis
use Illuminate\Support\Facades\Storage;  // Failų saugyklos fasadas
use Illuminate\Validation\ValidationException; // Validacijos išimčių klasė
use Carbon\Carbon;                  // Biblioteka darbui su datomis

// ProductController klasė paveldi iš bazinės Controller klasės
class ProductController extends Controller
{
    // --- Produktų Kūrimo Metodai (1 ir 2 Žingsniai) ---

    /**
     * Rodo pirmojo produkto kūrimo žingsnio formą.
     * @return \Illuminate\View\View
     */
    public function createStep1()
    {
        // Grąžina 'product.create-step1' Blade vaizdą
        return view('product.create-step1');
    }

    /**
     * Apdoroja pirmojo produkto kūrimo žingsnio formos duomenis.
     * Validuoja duomenis, išsaugo paveikslėlį (jei yra) ir įrašo duomenis į sesiją.
     * Nukreipia į antrąjį kūrimo žingsnį.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeStep1(Request $request)
    {
        // Validuoja gautus duomenis
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048', // Paveikslėlis neprivalomas, turi būti paveikslėlio tipo, max 2MB
        ]);

        // Jei buvo įkeltas paveikslėlis
        if ($request->hasFile('image')) {
            // Išsaugo paveikslėlį 'public/products' kataloge ir įrašo kelią į $validated masyvą
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Išsaugo validuotus duomenis sesijoje su raktu 'product.step1'
        Session::put('product.step1', $validated);
        // Nukreipia vartotoją į antrojo kūrimo žingsnio maršrutą
        return redirect()->route('product.create.step2');
    }

    /**
     * Rodo antrojo produkto kūrimo žingsnio formą.
     * Patikrina, ar sesijoje yra pirmojo žingsnio duomenys.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createStep2()
    {
        // Jei sesijoje nėra pirmojo žingsnio duomenų, nukreipia atgal su klaida
        if (!Session::has('product.step1')) {
            return redirect()->route('product.create.step1')->withErrors('Sesija baigėsi arba neteisinga.');
        }
        // Grąžina 'product.create-step2' Blade vaizdą
        return view('product.create-step2');
    }

    /**
     * Išsaugo naujai kuriamą produktą duomenų bazėje (apdoroja antrojo žingsnio formą).
     * Sujungia pirmojo ir antrojo žingsnio duomenis, sukuria produktą ir susijusią transakciją.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeStep2(Request $request)
    {
        // Validuoja antrojo žingsnio duomenis
        $validated = $request->validate([
            'expiration_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'weight' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        // Gauna pirmojo žingsnio duomenis iš sesijos
        $step1Data = Session::get('product.step1');
        // Jei pirmojo žingsnio duomenų nėra, nukreipia atgal
        if (!$step1Data) {
            return redirect()->route('product.create.step1')->withErrors('Trūksta pirmos dalies duomenų.');
        }

        // Sujungia abiejų žingsnių duomenis į vieną masyvą produkto kūrimui
        $productData = array_merge(
            $step1Data,
            $validated,
            [
                'user_id' => auth()->id(), // Priskiria produkto kūrėją (autentifikuotą vartotoją)
                'initial_quantity' => $validated['quantity'], // Pradinis kiekis nustatomas pagal įvestą kiekį
                'quantity' => $validated['quantity'],         // Dabartinis likutis taip pat nustatomas pagal įvestą kiekį
                'status' => 'Sandelyje',                     // Nustatomas pradinis statusas
            ]
        );

        try {
            // Sukuria naują produktą duomenų bazėje
            $product = Product::create($productData);

            // Sukuria pradinę produkto gavimo transakciją
            $product->transactions()->create([
                'user_id' => auth()->id(),
                'action_type' => 'Gauta',
                'quantity' => $product->initial_quantity, // Kiekis lygus pradiniam kiekiui
                'transaction_date' => $product->created_at, // Transakcijos data - produkto sukūrimo laikas
            ]);

            // Pašalina pirmojo žingsnio duomenis iš sesijos
            Session::forget('product.step1');
            // Nukreipia į prietaisų skydelį su patikrinimo pranešimu
            return redirect()->route('dashboard')->with('success', 'Produktas sėkmingai pridėtas!');

        } catch (\Exception $e) {
            // Klaidos atveju įrašo klaidą į log'us
            Log::error("Error creating product: " . $e->getMessage(), ['exception' => $e]);
            // Grąžina vartotoją į antrojo žingsnio formą su klaidos pranešimu ir išsaugotais įvesties duomenimis
            return back()->with('error', 'Įvyko klaida kuriant produktą. Bandykite dar kartą.')->withInput();
        }
    }

    // --- Produkto Redagavimo Metodas ---

    /**
     * Rodo formą nurodyto produkto metaduomenims redaguoti.
     *
     * @param  \App\Models\Product  $product Automatiškai įkeliamas Product modelis pagal maršrute perduotą ID (Route Model Binding).
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        // Autorizacijos patikrinimas: ar autentifikuotas vartotojas yra šio produkto savininkas
        if ($product->user_id !== auth()->id()) {
            abort(403); // Jei ne, grąžina 403 klaidą (Forbidden)
        }

        // Grąžina 'product.edit' Blade vaizdą, perduodant jam $product objektą
        return view('product.edit', compact('product'));
    }

    /**
     * Atnaujina nurodyto produkto metaduomenis ir pradinį kiekį duomenų bazėje.
     * Pakoreguoja dabartinį kiekį remiantis pradinio kiekio pokyčiu.
     * Registruoja pakeitimus (ProductChange) ir sukuria korekcijos transakciją (ProductTransaction).
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @param  \App\Models\Product  $product Automatiškai įkeliamas Product modelis.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        // Autorizacijos patikrinimas
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Neturite teisės redaguoti šio produkto.');
        }

        // Validuoja gautus duomenis (metaduomenys + pradinis kiekis)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'expiration_date' => 'required|date',
            'initial_quantity' => 'required|integer|min:0', // Pradinis kiekis privalomas, sveikasis skaičius, min 0
            'weight' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // --- Kiekio Korekcijos Skaičiavimas ---
        $originalData = $product->getOriginal(); // Gaunamos pradinės produkto reikšmės (prieš pakeitimus)
        $oldInitialQuantity = $originalData['initial_quantity'] ?? 0; // Senas pradinis kiekis
        $newInitialQuantity = $validated['initial_quantity'];      // Naujas pradinis kiekis iš formos
        $quantityAdjustment = $newInitialQuantity - $oldInitialQuantity; // Pradinio kiekio pokytis

        $currentQuantityInStock = $originalData['quantity'] ?? 0; // Dabartinis likutis sandėlyje
        // Apskaičiuojamas naujas dabartinis likutis, pridedant pradinio kiekio pokytį
        $newCurrentQuantityInStock = $currentQuantityInStock + $quantityAdjustment;

        // --- Validacija: Patikrinama, ar korekcija nesukuria neigiamo likučio ---
        if ($newCurrentQuantityInStock < 0) {
            $allocatedQuantity = $oldInitialQuantity - $currentQuantityInStock;
            // Grąžinama validacijos klaida su pranešimu
            throw ValidationException::withMessages([
                'initial_quantity' => 'Pradinis kiekis negali būti sumažintas tiek, kad dabartinis likutis (' . $currentQuantityInStock . ') taptų neigiamas. Jau paskirstyta ' . $allocatedQuantity . ' vnt.'
            ]);
        }

        // Jei atitinka, pridedamas pakoreguotas dabartinis kiekis į masyvą, kuris bus saugomas
        $validated['quantity'] = $newCurrentQuantityInStock;

        // --- Paveikslėlio Įkėlimo Tvarkymas ---
        if ($request->hasFile('image')) {
            // Jei produktas jau turėjo paveikslėlį, senasis ištrinamas
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Naujas paveikslėlis išsaugomas ir kelias įrašomas į $validated masyvą
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // --- Pakeitimų Registravimas (ProductChange) ---
        $changesMade = false;
        $now = now();
        // Einama per visus validuotus laukus
        foreach ($validated as $key => $newValue) {
            if ($key === 'image') continue;

            $originalValueForKey = $originalData[$key] ?? null; // Pradinė lauko reikšmė
            // Tikrinama, ar reikšmė tikrai pasikeitė
            if (!array_key_exists($key, $originalData) || (string) $originalValueForKey !== (string) $newValue) {
                ProductChange::create([ // Sukuriamas ProductChange įrašas
                    'product_id' => $product->id,
                    'field' => $key, // Pakeistas laukas
                    'old_value' => $originalValueForKey, // Sena reikšmė
                    'new_value' => $newValue,           // Nauja reikšmė
                    'changed_by' => auth()->id(),       // Kas pakeitė
                    'changed_at' => $now,              // Kada pakeitė
                ]);
                $changesMade = true; // Pažymima, kad buvo pakeitimų
            }
        }

        // --- Produkto Įrašo Atnaujinimas ---
        if ($changesMade) { // Jei buvo atlikti pakeitimai
            $product->update($validated); // Atnaujinamas produktas
            $successMessage = 'Produkto informacija atnaujinta!';
        } else {
            $successMessage = 'Jokie pakeitimai nebuvo atlikti.';
        }

        // --- Pradinio Kiekio Korekcijos Registravimas kaip Transakcija ---
        if ($quantityAdjustment != 0) { // Jei pradinis kiekis buvo pakeistas
            $product->transactions()->create([ // Sukuriama 'Korekcija' tipo transakcija
                'user_id' => auth()->id(),
                'action_type' => 'Korekcija',
                'quantity' => $quantityAdjustment, // Pokytis (gali būti teigiamas arba neigiamas)
                'transaction_date' => $now,
                'notes' => 'Pradinis kiekis pakoreguotas redagavimo metu.'
            ]);
        }

        // --- Nukreipimas Atgal ---
        // Grąžinama į produkto redagavimo puslapį su užtikrinimo pranešimu
        return redirect()->route('products.edit', $product)->with('success', $successMessage);
    }

    // --- Produktų Sąrašo Metodas ---

    /**
     * Rodo produktų sąrašo puslapį su filtravimo ir rikiavimo galimybėmis.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userId = Auth::id(); // Gaunamas autentifikuoto vartotojo ID

        // --- Reitingų/Žymų Skaičiavimas Filtravimui ---
        // Apskaičiuojami įvairūs rodikliai produktų žymėjimui (populiarus, top paaukotas ir t.t.)
        $soldSums = ProductTransaction::where('user_id', $userId)->where('action_type', 'Parduota')->select('product_id', DB::raw('SUM(quantity) as total_sold'))->groupBy('product_id')->pluck('total_sold', 'product_id');
        $donatedSums = ProductTransaction::where('user_id', $userId)->where('action_type', 'Paaukota')->select('product_id', DB::raw('SUM(quantity) as total_donated'))->groupBy('product_id')->pluck('total_donated', 'product_id');
        $maxSoldValue = !$soldSums->isEmpty() ? $soldSums->max() : 0;
        $maxDonatedValue = !$donatedSums->isEmpty() ? $donatedSums->max() : 0;
        $popularProductIds = ($maxSoldValue > 0) ? $soldSums->filter(fn($sum) => $sum == $maxSoldValue)->keys()->all() : [];
        $topDonatedProductIds = ($maxDonatedValue > 0) ? $donatedSums->filter(fn($sum) => $sum == $maxDonatedValue)->keys()->all() : [];
        $soldProductIds = $soldSums->keys();
        $allProductIds = Product::where('user_id', $userId)->pluck('id');
        $unpopularProductIds = $allProductIds->diff($soldProductIds)->all();
        $outOfStockProductIds = Product::where('user_id', $userId)->where('quantity', '<=', 0)->pluck('id')->all();
        // --- Reitingų Pabaiga ---

        // --- Bazinė Užklausa Produktams Gauti ---
        $query = Product::where('user_id', $userId); // Išrenkami tik dabartinio vartotojo produktai

        // --- Filtravimo Logika ---
        $tagFilter = $request->input('tag_filter', 'Visi');
        // Taikomi filtrai pagal pasirinktą žymą
        if ($tagFilter === 'Populiarus') { $query->whereIn('id', $popularProductIds); }
        elseif ($tagFilter === 'Top Paaukota') { $query->whereIn('id', $topDonatedProductIds); }
        elseif ($tagFilter === 'Nepopuliarus') { $query->whereIn('id', $unpopularProductIds); }
        elseif ($tagFilter === 'Nėra Likučio') { $query->whereIn('id', $outOfStockProductIds); }

        // Paieškos pagal pavadinimą filtras
        if ($request->filled('search')) { $query->where('name', 'like', '%' . $request->search . '%'); }

        // --- Rikiavimo Logika ---
        $validSortColumns = ['name', 'quantity', 'initial_quantity', 'price', 'expiration_date', 'created_at', 'updated_at']; // Leidžiami rikiavimo stulpeliai
        $sortBy = $request->input('sort_by', 'created_at'); // Pagal ką rikiuoti (numatytoji reikšmė 'created_at')
        $sortDir = $request->input('sort_dir', 'desc');    // Rikiavimo kryptis (numatytoji 'desc' - mažėjančia)
        // Patikrinama, ar rikiavimo parametrai yra teisingi
        if (!in_array($sortBy, $validSortColumns)) $sortBy = 'created_at';
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) $sortDir = 'desc';
        // --- Rikiavimo Pabaiga ---

        // Gaunami produktai su puslapiavimu ir pritaikytu rikiavimu
        $products = $query->orderBy($sortBy, $sortDir)->paginate(15); // 15 produktų per puslapį

        // Grąžinamas 'product.index' Blade vaizdas su reikiamais duomenimis
        return view('product.index', compact(
            'products',
            'sortBy',
            'sortDir',
            'tagFilter',
            'popularProductIds',
            'topDonatedProductIds',
            'unpopularProductIds'
        ));
    }

    // --- Produkto Šalinimo Metodas ---

    /**
     * Pašalina nurodytą produktą iš duomenų bazės.
     *
     * @param  \App\Models\Product  $product Automatiškai įkeliamas Product modelis.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Autorizacijos patikrinimas
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction(); // Pradedama duomenų bazės transakcija (saugumui)

            // Jei produktas turi paveikslėlį, jis ištrinamas iš saugyklos
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Ištrinamas pats produkto įrašas
            $product->delete();
            DB::commit(); // Patvirtinama transakcija
            // Nukreipiama į produktų sąrašą su patvirtinimo pranešimu
            return redirect()->route('products.index')->with('success', 'Produktas ištrintas sėkmingai!');
        } catch (\Exception $e) {
            DB::rollBack(); // Klaidos atveju transakcija atšaukiama
            // Įrašoma klaida į log'us
            Log::error("Error deleting product ID {$product->id}: " . $e->getMessage());
            // Nukreipiama į produktų sąrašą su klaidos pranešimu
            return redirect()->route('products.index')->with('error', 'Klaida trinant produktą.');
        }
    }

}
