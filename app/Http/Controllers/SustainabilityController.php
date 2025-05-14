<?php

namespace App\Http\Controllers;

use App\Services\SustainabilityService; // Klase pagrindiniams tvarumo rodikliams apskaičiuoti
use Illuminate\Http\Request;           // Standartinis HTTP užklausos objektas
use App\Models\Product;                // Produkto modelis
use App\Models\User;                   // Vartotojo modelis
use App\Models\ProductTransaction;    // Produkto transakcijų modelis
use Carbon\Carbon;                     // Biblioteka darbui su datomis ir laiku
use Illuminate\Support\Facades\Auth;   // Autentifikacijos fasadas vartotojo informacijai gauti
use Illuminate\Support\Collection;     // Laravel kolekcijų klasė
use Illuminate\Support\Facades\Log;  // Log'inimo fasadas debugams
use Illuminate\Support\Facades\DB;   // Duomenų bazės fasadas

// SustainabilityController klasė paveldi iš bazinės Controller klasės
class SustainabilityController extends Controller
{
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
     * Rodo tvarumo statistikos puslapį.
     * Surenka įvairius duomenis apie vartotojo veiklą, produktus, pajamas,
     * tvarumo rodiklius ir transakcijų istoriją, ir perduoda juos į 'sustainability.index' vaizdą.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Grąžina vaizdą su duomenimis arba nukreipia į prisijungimą.
     */
    public function index()
    {
        // Gaunamas šiuo metu autentifikuotas vartotojas
        $user = Auth::user();
        // Jei vartotojas neautentifikuotas, nukreipiama į prisijungimo puslapį
        if (!$user) {
            return redirect()->route('login')->with('error', 'Prašome prisijungti.');
        }

        // === Pagrindinė tvarumo statistika iš SustainabilityService ===
        // Servisas apskaičiuoja tvarumo procentą, ženkliuko kelią, išmesto/sutaupyto svorio kg, CO2 ir kt.
        $stats = $this->sustainabilityService->getStatsForUser($user);

        // --- Duomenys diagramoms ---
        // Savaitės pardavimai (vienetais) pagal dienas
        $weeklySales = $this->calculateWeeklySalesUnits($user->id);
        // Dažniausiai išmetami produktai (pagal vienetus)
        $mostWasted = $this->calculateMostWastedProducts($user->id);
        // --- Diagramų duomenų pabaiga ---


        // +++ DUOMENYS STATISTIKOS BLOKAMS  +++

        // --- Šiandienos pajamų skaičiavimas ---
        $transactionsToday = ProductTransaction::with('product:id,price')
        ->where('user_id', $user->id)
            ->where('action_type', 'Parduota')
            ->whereDate('transaction_date', Carbon::today()) // Tik šiandienos transakcijos
            ->get();
        $todaysIncome = $transactionsToday->sum(function($transaction) {
            $price = $transaction->product->price ?? 0; // Produkto kaina (arba 0, jei nėra)
            $quantity = max(0, $transaction->quantity);  // Kiekis (užtikrinam, kad ne neigiamas)
            return $quantity * $price;
        });

        // --- Rezervuotų vienetų skaičiavimas ---
        $reservedQuantity = (int) ProductTransaction::where('user_id', $user->id)
            ->where('action_type', 'Rezervuota')->sum('quantity');
        $reservedQuantity = max(0, $reservedQuantity); // Užtikrinam, kad ne neigiamas

        // --- Populiariausio produkto radimas (pagal parduotą kiekį) ---
        $topData = ProductTransaction::where('user_id', $user->id)
            ->where('action_type', 'Parduota')
            // Renkamės produkto ID ir sumuojame parduotus kiekius (tik teigiamus)
            ->select('product_id', DB::raw('SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as total_quantity_sold'))
            ->groupBy('product_id') // Grupuojae pagal produkto ID
            ->orderByDesc('total_quantity_sold') // Rikiuojame pagal parduotą kiekį mažėjančia tvarka
            ->first(); // Imame pirmą (populiariausią)
        $topProduct = null;
        $topSoldProductData = null;
        if ($topData && $topData->total_quantity_sold > 0) {
            $topProduct = Product::find($topData->product_id);
            $topSoldProductData = $topData; // Išsaugome pardavimo duomenis
        }

        // --- Ženkliuko kelio gavimas iš $stats ---
        $badgePath = $stats['badgePath'] ?? null;

        // --- Savaitės pajamų skaičiavimas ---
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $transactionsThisWeek = ProductTransaction::with('product:id,price')
            ->where('user_id', $user->id)
            ->where('action_type', 'Parduota')
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek]) // Transakcijos šią savaitę
            ->get();
        $weeklyIncome = $transactionsThisWeek->sum(function($transaction) {
            $price = $transaction->product->price ?? 0;
            $quantity = max(0, $transaction->quantity);
            return $quantity * $price;
        });

        // --- Bendra sandėlio kiekių suma ---
        $totalStockQuantity = (int) Product::where('user_id', $user->id)->sum('quantity');
        $totalStockQuantity = max(0, $totalStockQuantity); // Užtikrinam, kad ne neigiamas
        // +++ STATISTIKOS BLOKŲ DUOMENŲ PABAIGA +++


        // +++ TRANSAKCIJŲ ISTORIJOS GAVIMAS +++
        $transactionHistory = ProductTransaction::with('product:id,name') // Kartu paimam produkto pavadinimą
        ->where('user_id', $user->id)
            // Galima filtruoti pagal tipą, pvz., nerodyti 'Korekcija', jei toks tipas egzistuoja ir nenorima jo rodyti
            ->orderBy('transaction_date', 'desc') // Rikiuojam pagal transakcijos datą (naujausios viršuje)
            ->orderBy('id', 'desc')               // Papildomas rikiavimas pagal ID (jei datos ir laikai sutampa)
            ->take(20)                            // Imam paskutinius 20 įrašų
            ->get();
        // +++ TRANSAKCIJŲ ISTORIJOS GAVIMO PABAIGA +++

        // Perduodame visus surinktus duomenis į 'sustainability.index' vaizdą
        return view('sustainability.index', [
            'wasted' => $stats['wastedKg'], // Išmesta kg
            'sold' => $stats['soldUnits'],    // Parduota vnt.
            'savedCO2' => $stats['savedCO2'],
            'sustainabilityPercentage' => $stats['sustainabilityPercentage'],
            'weeklySales' => $weeklySales ?? collect([]), // Savaitės pardavimai diagramai
            'leastSold' => $mostWasted ?? collect([]),   // Mažiausiai parduoti/dažniausiai išmesti
            'todaysIncome' => $todaysIncome ?? 0,
            'reservedQuantity' => $reservedQuantity ?? 0,
            'topProduct' => $topProduct,
            'topSoldProductData' => $topSoldProductData,
            'badgePath' => $badgePath,
            'weeklyIncome' => $weeklyIncome ?? 0,
            'totalStockQuantity' => $totalStockQuantity ?? 0,
            'transactionHistory' => $transactionHistory ?? collect([]), // Perduodama transakcijų istorija
        ]);
    }

    /**
     * Pagalbinis metodas savaitės pardavimų vienetams apskaičiuoti pagal dienas.
     * Naudojamas 'Savaitės pardavimų apžvalga' diagramai.
     *
     * @param int $userId Vartotojo ID.
     * @return \Illuminate\Support\Collection Kolekcija su parduotais vienetais kiekvienai savaitės dienai.
     */
    private function calculateWeeklySalesUnits(int $userId): Collection
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        // Naudojam savaitės dienų numeraciją (0=Sekmadienis, 1=Pirmadienis...),
        $lithuanianDays = collect([1 => 'Pi', 2 => 'An', 3 => 'Tr', 4 => 'Ke', 5 => 'Pe', 6 => 'Še', 0 => 'Se']); // 0 yra Sekmadienis pagal strftime('%w') SQLite

        // Pradinis masyvas su visomis savaitės dienomis ir 0 pardavimų
        $dailySalesUnits = collect(['Pi' => 0, 'An' => 0, 'Tr' => 0, 'Ke' => 0, 'Pe' => 0, 'Še' => 0, 'Se' => 0]);

        // Gaunami pardavimų duomenys, sugrupuoti pagal savaitės dieną
        $salesData = ProductTransaction::where('user_id', $userId)
            ->where('action_type', 'Parduota')
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            // strftime('%w', ...) SQLite grąžina savaitės dieną (0-6, 0=Sekmadienis)
            ->selectRaw('strftime(\'%w\', transaction_date) as iso_day_num, SUM(quantity) as total_units')
            ->groupBy('iso_day_num')
            ->pluck('total_units', 'iso_day_num'); // Grąžina masyvą [savaitės_diena_nr => suma]

        // Sujungiami gauti duomenys su pradiniu masyvu, kad būtų visos dienos
        foreach ($salesData as $dayNum => $units) {
            $dayNum = (int)$dayNum; // Konvertuojam į skaičių
            if ($lithuanianDays->has($dayNum)) {
                $dayKey = $lithuanianDays->get($dayNum);
                $dailySalesUnits->put($dayKey, max(0, (int)$units));
            }
        }
        // Grąžinama galutinė kolekcija su pardavimais kiekvienai dienai
        return $dailySalesUnits;
    }


    /**
     * Pagalbinis metodas dažniausiai išmetamų produktų sąrašui gauti.
     * Naudojamas 'Dažniausiai išmetami produktai' diagramai.
     *
     * @param int $userId Vartotojo ID.
     * @return \Illuminate\Support\Collection Kolekcija su produkto pavadinimu ir išmestu kiekiu.
     */
    private function calculateMostWastedProducts(int $userId): Collection
    {
        // Gaunami duomenys apie išmestus produktus, kartu su produkto pavadinimu
        $mostWastedData = ProductTransaction::with('product:id,name')
        ->where('user_id', $userId)
            ->where('action_type', 'Išmesta')
            ->whereHas('product') // Užtikrinam, kad susijęs produktas egzistuoja
            ->select('product_id', DB::raw('SUM(quantity) as total_wasted')) // Susumuojam išmestą kiekį
            ->groupBy('product_id') // Grupuojam pagal produkto ID
            ->having('total_wasted', '>', 0) // Imam tik tuos, kur išmesta > 0
            ->orderByDesc('total_wasted') // Rikiuojam pagal išmestą kiekį mažėjančia tvarka
            ->take(5) // Imam top 5 produktus
            ->get();

        // Konvertuojama kolekcija į formatą ['Produkto Pavadinimas' => kiekis]
        return $mostWastedData->mapWithKeys(function($item) {
            // Jei produktas dėl kažkokios priežasties nerastas naudojam ID
            $productName = $item->product ? $item->product->name : ('Nežinomas produktas (ID: ' . $item->product_id . ')');
            return [$productName => (int)$item->total_wasted]; // Užtikrinam, kad kiekis yra sveikasis skaičius
        });
    }
}
