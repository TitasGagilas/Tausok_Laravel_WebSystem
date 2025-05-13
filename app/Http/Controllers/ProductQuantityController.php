<?php

namespace App\Http\Controllers;

use App\Models\Product;             // Produkto modelis
use App\Models\ProductTransaction; // Produkto transakcijų modelis
use Illuminate\Http\Request;        // HTTP užklausos objektas
use Illuminate\Support\Facades\DB;   // Duomenų bazės fasadas
use Illuminate\Support\Facades\Auth; // Autentifikacijos fasadas
use Illuminate\Validation\Rule;      // Validacijos taisyklėms
use Illuminate\Support\Collection;   // Laravel kolekcijų klasė
use Illuminate\Support\Facades\Log;  // Log'inimo fasadas klaidų registravimui
use Carbon\Carbon;                   // Biblioteka darbui su datomis

// ProductQuantityController klasė paveldi iš bazinės Controller klasės
class ProductQuantityController extends Controller
{
    /**
     * Rodo produktų kiekių valdymo puslapį.
     * Gauna vartotojo produktus puslapiavimui.
     * Vaizde (view) naudojami produktų modelio (accessors)
     * kaip $product->sold_quantity dabartiniams kiekiams gauti.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // Gaunamas autentifikuotas vartotojas
        // Išrenkami vartotojo produktai, rikiuojami pagal sukūrimo datą, naudojamas puslapiavimas (20 produktų per puslapį)
        $products = Product::where('user_id', $user->id)
            ->latest('created_at') // Rikiuoja pagal 'created_at' mažėjančia tvarka
            ->paginate(20);

        // Grąžina 'product.quantity-management' Blade vaizdą, perduodant jam $products kintamąjį
        return view('product.quantity-management', compact('products'));
    }

    /**
     * Išsaugo atnaujintus kiekių paskirstymus iš valdymo puslapio formos.
     * Apskaičiuoja kiekių pokyčius palyginus su esamomis sumomis
     * ir sukuria naujas ProductTransaction įrašus šiems pokyčiams.
     * Naudoja pasirinktą datą transakcijoms.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user(); // Gaunamas autentifikuotas vartotojas
        // Gaunami visi pateikti kiekiai iš formos, jei nėra - tuščias masyvas
        $allQuantitiesInput = $request->input('quantities', []);
        $errors = []; // Masyvas validacijos klaidoms rinkti (pagal produktą)
        $changedProductIds = []; // Masyvas produktų ID, kuriems buvo atlikti pakeitimai

        // Naudojama duomenų bazės transakcija: jei bent vienas produktas nepavyksta,
        // visi pakeitimai atšaukiami (rollback).
        DB::beginTransaction();

        try {
            // --- Datos gavimas ir validacija ---
            // Gaunama data iš formos; jei nėra, naudojama šiandienos data
            $targetDateString = $request->input('target_date', now()->toDateString());

            $request->validate([
                'target_date' => 'required|date|before_or_equal:today',
            ], [
                'target_date.required' => 'Transakcijos data yra privaloma.',
                'target_date.date' => 'Neteisingas datos formatas.',
                'target_date.before_or_equal' => 'Negalima įvesti transakcijų ateities datai.',
            ]);

            // Konvertuojama datos eilutė į Carbon objektą
            try {
                $transactionTimestamp = Carbon::parse($targetDateString)->startOfDay();
            } catch (\Exception $e) {
                // Klaidos atveju atšaukiama transakcija ir grąžinama klaida
                DB::rollBack();
                return back()->withErrors(['target_date' => 'Neteisingas datos formatas.'])->withInput();
            }
            // --- Datos gavimo ir validacijos pabaiga ---

            // Einama per kiekvieną produktą, kuriam buvo pateikti duomenys formoje
            foreach ($allQuantitiesInput as $productId => $newStatusQuantities) {

                // Surandamas produktas, užtikrinant, kad jis priklauso dabartiniam vartotojui.
                // Kartu užkraunamos susijusios transakcijos (reikalingos žymoms, pvz., $product->sold_quantity).
                $product = Product::with('transactions')
                    ->where('user_id', $user->id)
                    ->find($productId);

                // Jei produktas nerastas arba nepriklauso vartotojui, praleidžiamas ir įrašoma į log'us
                if (!$product) {
                    Log::warning("ProductQuantityController::store - Product ID {$productId} not found or doesn't belong to user ID {$user->id}.");
                    continue; // Pereinama prie kito produkto cikle
                }

                $initialQty = $product->initial_quantity ?? 0; // Gaunamas pradinis produkto kiekis

                // --- Dabartinių suminių kiekių apskaičiavimas pagal esamas transakcijas  ---

                $currentSold = $product->sold_quantity;     // Dabartinis parduotas kiekis
                $currentDonated = $product->donated_quantity; // Dabartinis paaukotas kiekis
                $currentWasted = $product->wasted_quantity;  // Dabartinis išmestas kiekis
                $currentReserved = $product->reserved_quantity; // Dabartinis rezervuotas kiekis

                // --- Naujų suminių kiekių gavimas iš formos (užtikrinant, kad jie teigiami sveikieji skaičiai) ---
                $newSold = max(0, (int)($newStatusQuantities['Parduota'] ?? 0));
                $newDonated = max(0, (int)($newStatusQuantities['Paaukota'] ?? 0));
                $newWasted = max(0, (int)($newStatusQuantities['Išmesta'] ?? 0));
                $newReserved = max(0, (int)($newStatusQuantities['Rezervuota'] ?? 0));

                // --- Serverio pusės validacija ---
                // Bendra išeinanti suma (parduota + paaukota + išmesta)
                $outgoingTotal = $newSold + $newDonated + $newWasted;
                // Naujas likutis sandėlyje apskaičiuojamas iš pradinio kiekio atimant išeinančius
                $newInShop = $initialQty - $outgoingTotal;

                // Tikrinama, ar naujas likutis sandėlyje nėra neigiamas
                if ($newInShop < 0) {
                    $errors["quantities.{$productId}.balance"] = "Paskirstyta ({$outgoingTotal}) daugiau nei pradinis kiekis ({$initialQty}) produktui '{$product->name}'.";
                    continue; // Pereinama prie kito produkto, šio pakeitimai nebus išsaugoti
                }
                // Tikrinama, ar rezervuotas kiekis neviršija naujo likučio sandėlyje
                if ($newReserved > $newInShop) {
                    $errors["quantities.{$productId}.Rezervuota"] = "Rezervuotas kiekis ({$newReserved}) negali viršyti likučio sandėlyje ({$newInShop}) produktui '{$product->name}'.";
                    continue; // Pereinama prie kito produkto
                }

                // --- Pokyčių apskaičiavimas ---
                // Apskaičiuojama, kiek kiekvieno tipo vienetų reikia pridėti ar atimti,
                // kad pasiektume naujas sumines reikšmes, įvestas formoje.
                $deltaSold = $newSold - $currentSold;
                $deltaDonated = $newDonated - $currentDonated;
                $deltaWasted = $newWasted - $currentWasted;
                $deltaReserved = $newReserved - $currentReserved;

                // --- Transakcijų kūrimas pokyčiams ---
                $transactionsToCreate = []; // Masyvas naujoms transakcijoms kaupti

                // Jei yra pokytis, ruošiama transakcija
                if ($deltaSold != 0) $transactionsToCreate[] = ['action_type' => 'Parduota', 'quantity' => $deltaSold];
                if ($deltaDonated != 0) $transactionsToCreate[] = ['action_type' => 'Paaukota', 'quantity' => $deltaDonated];
                if ($deltaWasted != 0) $transactionsToCreate[] = ['action_type' => 'Išmesta', 'quantity' => $deltaWasted];
                if ($deltaReserved != 0) $transactionsToCreate[] = ['action_type' => 'Rezervuota', 'quantity' => $deltaReserved];

                // Jei buvo kokių nors pokyčių (reikia kurti transakcijas)
                if (!empty($transactionsToCreate)) {
                    $transactionData = []; // Masyvas duomenims, kurie bus įrašyti į DB
                    $currentTimestampForEloquent = now(); // Dabartinis laikas Eloquent created_at/updated_at stulpeliams

                    foreach ($transactionsToCreate as $tx) {
                        $transactionData[] = [
                            'user_id' => $user->id,
                            'product_id' => $productId,      // Priskiriamas produkto ID
                            'action_type' => $tx['action_type'],
                            'quantity' => $tx['quantity'],   // Pokytis
                            'transaction_date' => $transactionTimestamp, // Naudojama vartotojo pasirinkta data
                            'created_at' => $currentTimestampForEloquent, // Sukūrimo laikas - dabartinis
                            'updated_at' => $currentTimestampForEloquent, // Atnaujinimo laikas - dabartinis
                        ];
                    }
                    // Sukuriamos visos transakcijos vienu metu arba cikle per modelį
                    // $product->transactions()->createMany($transactionData); // Buvo problema su product_id
                    foreach($transactionData as $tData) {
                        ProductTransaction::create($tData); // Kuriama kiekviena transakcija atskirai per modelį
                    }

                    // Atnaujinamas produkto likutis sandėlyje (`quantity` stulpelis `products` lentelėje)
                    $product->quantity = $newInShop;
                    $product->save(); // Išsaugomi produkto pakeitimai
                    $changedProductIds[] = $productId; // Pridedamas produkto ID į pakeistų sąrašą
                }
            } // Ciklo per $allQuantitiesInput pabaiga

            // Jei buvo validacijos klaidų per produktų ciklą
            if (!empty($errors)) {
                DB::rollBack(); // Atšaukiama duomenų bazės transakcija
                return back()->withErrors($errors)->withInput(); // Grąžinama atgal su klaidomis ir įvestimi
            }

            DB::commit(); // Jei viskas gerai, patvirtinama duomenų bazės transakcija

            // Formuojamas tvirtinimo pranešimas
            $message = count($changedProductIds) > 0 ? 'Produktų kiekiai sėkmingai atnaujinti pasirinktai datai.' : 'Nebuvo atlikta jokių pakeitimų.';
            // Nukreipiama į kiekių valdymo puslapį su tvirtinimo pranešimu
            return redirect()->route('products.quantity.index')->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) { // Sugaudoma Laravel validacijos išimtis
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) { // Sugaudoma bet kokia kita bendra klaida
            DB::rollBack();
            Log::error("Error storing product quantities: " . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Įvyko sisteminė klaida išsaugant pakeitimus. Bandykite dar kartą.')->withInput();
        }
    }
}
