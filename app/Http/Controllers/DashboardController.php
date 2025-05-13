<?php

namespace App\Http\Controllers;

// Importuojamos reikalingos klasės ir fasadai
use App\Services\SustainabilityService; // Servisas tvarumo ir ženkliukų skaičiavimui
use Illuminate\Support\Facades\DB;      // Duomenų bazės fasadas tiesioginėms SQL užklausoms
use Illuminate\Http\Request;           // Standartinis HTTP užklausos objektas
use Illuminate\Support\Facades\Auth;   // Autentifikacijos fasadas vartotojo informacijai gauti
use App\Models\Product;                // Produkto modelis
use App\Models\ProductTransaction;    // Produkto transakcijų modelis
use Carbon\Carbon;                     // Biblioteka darbui su datomis ir laiku

class DashboardController extends Controller
{
    // Savybė (property) SustainabilityService objektui laikyti
    protected $sustainabilityService;

    /**
     * Konstruktorius.
     * SustainabilityService yra įdiegiamas (injected) per konstruktorių (Dependency Injection).
     * Tai leidžia naudoti SustainabilityService metodus šiame kontroleryje.
     *
     * @param SustainabilityService $sustainabilityService SustainabilityService objekto instancija.
     */
    public function __construct(SustainabilityService $sustainabilityService)
    {
        $this->sustainabilityService = $sustainabilityService;
    }

    /**
     * Rodo pagrindinį aplikacijos puslapį (dashboard) su apskaičiuota statistika.
     *
     * Šis metodas surenka įvairią informaciją apie vartotojo produktus,
     * transakcijas, pajamas, tvarumo rodiklius ir perduoda ją į 'dashboard' vaizdą (view).
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse Grąžina dashboard vaizdą su duomenimis arba nukreipia į prisijungimo puslapį, jei vartotojas neautentifikuotas.
     */
    public function index()
    {
        // Gaunamas šiuo metu autentifikuotas vartotojas
        $user = Auth::user();

        // Jei vartotojas neautentifikuotas, nukreipiama į prisijungimo puslapį
        if (!$user) {
            return redirect()->route('login');
        }

        // --- Gaunama tvarumo statistika iš SustainabilityService ---
        // Servisas apskaičiuoja procentus, ženkliuko kelią, kg, CO2 ir kt.
        $stats = $this->sustainabilityService->getStatsForUser($user);

        // --- Gaunami produktai sąrašui prietaisų skydelyje ---
        // Išrenkami vartotojo produktai, kurių kiekis > 0, rikiuojami pagal sukūrimo datą (naujausi viršuje)
        $productsForList = Product::where('user_id', $user->id)
            ->where('quantity', '>', 0) // Tik tie produktai, kurių yra sandėlyje
            ->latest('created_at') // Rikiuojama pagal 'created_at' stulpelį mažėjančia tvarka
            ->get(); // Gaunami visi atitinkantys įrašai

        // --- Šiandienos pajamų skaičiavimas ---
        // Išrenkamos visos šiandienos 'Parduota' tipo transakcijos kartu su produkto kaina
        $transactionsToday = ProductTransaction::with('product:id,price')
        ->where('user_id', $user->id) // Tik dabartinio vartotojo transakcijos
        ->where('action_type', 'Parduota') // Tik pardavimo transakcijos
        ->whereDate('transaction_date', Carbon::today()) // Tik šiandienos datos transakcijos
        ->get();
        // Susumuojamos pajamos: kiekvienos transakcijos kiekis padauginamas iš produkto kainos
        $todaysIncome = $transactionsToday->sum(function($transaction) {
            $quantity = max(0, $transaction->quantity); // Užtikrinama, kad kiekis nebūtų neigiamas
            return $quantity * ($transaction->product->price ?? 0); // Padauginama iš kainos
        });

        // --- Rezervuotų vienetų skaičiavimas ---
        // Susumuojamas 'Rezervuota' tipo transakcijų kiekis
        $reservedSum = ProductTransaction::where('user_id', $user->id)
            ->where('action_type', 'Rezervuota')->sum('quantity');
        $reservedQuantity = max(0, (int)$reservedSum); // Užtikrinama, kad kiekis nebūtų neigiamas ir būtų sveikasis skaičius

        // --- Populiariausio produkto radimas (pagal parduotą kiekį) ---
        $topData = ProductTransaction::where('user_id', $user->id)
            ->where('action_type', 'Parduota')
            // Parenkamas produkto ID ir susumuojamas parduotas kiekis (tik teigiamos reikšmės)
            ->select('product_id', DB::raw('SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as total_quantity_sold'))
            ->groupBy('product_id') // Grupuojama pagal produkto ID
            ->orderByDesc('total_quantity_sold') // Rikiuojama pagal parduotą kiekį mažėjančia tvarka
            ->first(); // Paimamas pirmas įrašas

        $topProduct = null; // Produktas (Modelio objektas)
        $topSoldProductData = null; // Duomenys apie pardavimus (su suma)
        // Jei rastas populiariausias produktas ir jo parduotas kiekis > 0
        if ($topData && $topData->total_quantity_sold > 0) {
            $topProduct = Product::find($topData->product_id); // Surandamas pats produktas pagal ID
            $topSoldProductData = $topData; // Išsaugomi duomenys
        }
        // --- Pabaiga:Dashboard skaičiavimai ---

        // --- Duomenų perdavimas į 'dashboard' vaizdą (view) ---
        // Visi surinkti ir apskaičiuoti duomenys perduodami kaip asociatyvus masyvas
        return view('dashboard', [
            // Duomenys produktų sąrašo sekcijai
            'products' => $productsForList, // Naudojamas aiškesnis pavadinimas

            // Duomenys iš SustainabilityService (įskaitant ženkliuko kelią)
            'sustainabilityPercentage' => $stats['sustainabilityPercentage'],
            'badgePath' => $stats['badgePath'],
            'wastedKg' => $stats['wastedKg'],    // Išmesta kg
            'soldKg' => $stats['savedKg'],       // Parduota/išsaugota kg
            'savedCO2' => $stats['savedCO2'],    // Išsaugota CO2

            // Apskaičiuota dashboard statistika
            'todaysIncome' => $todaysIncome,        // Šiandienos pajamos
            'reservedQuantity' => (int) $reservedQuantity, // Rezervuoti vnt.
            'topProduct' => $topProduct,             // Populiariausias produktas
            'topSoldProductData' => $topSoldProductData, // Populiariausio produkto pardavimų duomenys
        ]);
    }
}
