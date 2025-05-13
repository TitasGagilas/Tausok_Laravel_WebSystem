<?php

namespace App\Services;

use App\Models\Product;             // Produkto modelis
use App\Models\User;                // Vartotojo modelis
use App\Models\ProductTransaction; // Produkto transakcijų modelis
use Carbon\Carbon;                  // Biblioteka darbui su datomis
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @class SustainabilityService
 * @brief Klasė, atsakingas už įvairių tvarumo rodiklių apskaičiavimą.
 *
 * Apskaičiuoja tvarumo procentą, nustato pasiekimų ženkliuką,
 * skaičiuoja išmestų ir išsaugotų produktų svorį kilogramais bei
 * sutaupytą CO2 kiekį remiantis vartotojo produktų transakcijomis.
 *
 * @package App\Services
 */
class SustainabilityService
{
    /**
     * Apskaičiuoja įvairius tvarumo statistinius rodiklius nurodytam vartotojui.
     * Ši versija ženkliukų kriterijus skaičiuoja pagal SUMINIUS kiekius iš transakcijų.
     *
     * @param User $user Vartotojo objektas, kuriam skaičiuojama statistika.
     * @return array Asociatyvus masyvas su apskaičiuotais statistiniais rodikliais.
     */
    public function getStatsForUser(User $user): array
    {
        $userId = $user->id; // Vartotojo ID

        // --- Reikiamų duomenų gavimas ---
        // Gaunamos visos vartotojo transakcijos kartu su susijusio produkto ID, svoriu ir pradiniu kiekiu.
        // 'product:id,weight,initial_quantity' yra optimizacija (eager loading), kad būtų užkrauti tik reikalingi produkto stulpeliai.
        $transactions = ProductTransaction::with('product:id,weight,initial_quantity')
            ->where('user_id', $userId)
            ->get(); // Gaunama transakcijų kolekcija

        // Apskaičiuojamas pradinis visų vartotojo produktų vienetų kiekis
        $initialTotalUnits = (int) Product::where('user_id', $userId)->sum('initial_quantity');

        // --- Vienetų sumų skaičiavimas iš transakcijų ---
        // Susumuojami kiekiai pagal transakcijos tipą
        $soldUnits = (int) $transactions->where('action_type', 'Parduota')->sum('quantity');    // Parduota vnt.
        $donatedUnits = (int) $transactions->where('action_type', 'Paaukota')->sum('quantity');  // Paaukota vnt.
        $wastedUnits = (int) $transactions->where('action_type', 'Išmesta')->sum('quantity');    // Išmesta vnt.

        // "Gerųjų" vienetų suma (parduota + paaukota)
        $goodUnits = $soldUnits + $donatedUnits;

        // --- Ženkliuko nustatymas ---
        // Kviečiamas pagalbinis metodas ženkliukui nustatyti pagal apskaičiuotus kiekius
        $badgePath = $this->determineBadgePath($soldUnits, $donatedUnits, $wastedUnits, $goodUnits);

        // --- Tvarumo procento skaičiavimas ---
        // Formulė: ((Pradinis Kiekis - Išmestas Kiekis) / Pradinis Kiekis) * 100%
        // Jei pradinis kiekis 0: jei buvo transakcijų - 0%, jei nebuvo - 100% (prielaida, kad nieko nešvaistyta).
        $sustainabilityPercentage = $initialTotalUnits > 0
            ? round(($initialTotalUnits - $wastedUnits) / $initialTotalUnits * 100)
            : ($transactions->count() > 0 ? 0 : 100); // Saugiklis nuo dalybos iš nulio

        // --- Kilogramų / CO2 sutaupymo skaičiavimas pagal transakcijas ---
        $savedKg = 0;  // Išsaugota kilogramais
        $wastedKg = 0; // Išmesta kilogramais
        // Veiksmų tipai, kurie laikomi "išsaugojimu" (prisideda prie savedKg)
        $savedActionTypes = ['Parduota', 'Paaukota', 'Rezervuota']; // 'Rezervuota' prideda prie išsaugotų, nes produktas neišmestas

        // Einama per kiekvieną transakciją
        foreach ($transactions as $transaction) {
            if (!$transaction->product) continue;

            // Konvertuojamas produkto vieneto svoris į kilogramus
            $unitKg = $this->parseWeightToKg($transaction->product->weight ?? '0');
            // Apskaičiuojamas bendras transakcijos svoris kg
            $transactionWeight = $transaction->quantity * $unitKg;

            // Priklausomai nuo veiksmo tipo, pridedama prie išmesto arba išsaugoto svorio
            if ($transaction->action_type === 'Išmesta') {
                $wastedKg += $transactionWeight;
            } elseif (in_array($transaction->action_type, $savedActionTypes)) {
                // Atsižvelgiama į transakcijos kiekio ženklą (pvz., jei 'Parduota' neigiama, tai mažina savedKg)
                // Pagal ProductQuantityController logiką, 'Parduota', 'Paaukota', 'Išmesta' turėtų būti teigiamos,
                $savedKg += $transactionWeight;
            }
        }

        // Užtikrinama, kad svoriai nebūtų neigiami (dėl galimų korekcijų ar neįprastų transakcijų)
        $savedKg = max(0, $savedKg);
        $wastedKg = max(0, $wastedKg);

        // Apskaičiuojamas sutaupytas CO2 (prielaida: 1kg sutaupyto maisto = 1.6kg CO2)
        $savedCO2 = $savedKg * 1.6;

        // Grąžinamas masyvas su visais apskaičiuotais statistiniais rodikliais
        return [
            'sustainabilityPercentage' => $sustainabilityPercentage, // Tvarumo procentas
            'badgePath' => $badgePath,                         // Kelias iki ženkliuko paveikslėlio
            'wastedKg' => round($wastedKg, 2),                 // Išmestas svoris kg (suapvalinta)
            'savedKg' => round($savedKg, 2),                   // Išsaugotas/Parduotas/Paaukotas svoris kg (suapvalinta)
            'savedCO2' => round($savedCO2, 2),                 // Sutaupytas CO2 kg (suapvalinta)
            'wastedUnits' => $wastedUnits,                     // Išmesta vienetais
            'soldUnits' => $soldUnits,                         // Parduota vienetais
        ];
    }

    /**
     * Pagalbinis metodas svorio eilutei konvertuoti į kilogramus (float).
     * Bando apdoroti įvairius formatus, pvz., "500g", "1.5kg".
     *
     * @param string $weight Svorio eilutė (pvz., "500g", "1kg").
     * @return float Svoris kilogramais.
     */
    private function parseWeightToKg(string $weight): float
    {
        $weightStr = strtolower($weight); // Konvertuojama į mažąsias raides
        // Išfiltruojamas skaičius iš eilutės
        $value = (float) filter_var($weightStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $unitKg = 0; // Pradinė reikšmė

        // Tikrinama, ar eilutėje yra 'kg', 'g' ir atitinkamai konvertuojama
        if (str_contains($weightStr, 'kg')) {
            $unitKg = $value;
        } elseif (str_contains($weightStr, 'g')) {
            $unitKg = $value / 1000; // Gramai į kilogramus
        } elseif (str_contains($weightStr, 'l')) {
            $unitKg = $value; // Litrus prilyginam kilogramams (apytiksliai vandeniui)
        } elseif (str_contains($weightStr, 'ml')) {
            $unitKg = $value / 1000; // Mililitrus į kilogramus (apytiksliai vandeniui)
        }
        // Jei nėra aiškaus vieneto, grąžinamas 0 arba skaičius be konversijos,
        // priklausomai nuo to, kaip norima interpretuoti neaiškius įrašus.
        // Šiuo metu, jei nėra 'kg', 'g', 'l', 'ml', $unitKg liks 0, nebent $value buvo tiesiog skaičius.

        return $unitKg;
    }

    /**
     * Nustato kelio iki pasiekimų ženkliuko paveikslėlio remiantis
     * parduotų, paaukotų ir išmestų vienetų skaičiais bei švaistymo santykiu.
     *
     * @param int $soldUnits Parduota vienetų.
     * @param int $donatedUnits Paaukota vienetų.
     * @param int $wastedUnits Išmesta vienetų.
     * @param int $goodUnits "Gerųjų" vienetų suma (parduota + paaukota).
     * @return string|null Kelias iki ženkliuko paveikslėlio arba null, jei neatitinka kriterijų.
     */
    private function determineBadgePath(int $soldUnits, int $donatedUnits, int $wastedUnits, int $goodUnits): ?string
    {
        // Apskaičiuojamas švaistymo santykis: Išmesta / (Parduota + Paaukota)
        // Saugiklis nuo dalybos iš nulio, jei $goodUnits yra 0.
        $wasteRatio = ($goodUnits > 0) ? ($wastedUnits / $goodUnits) : PHP_INT_MAX; // Jei nėra gerų, švaistymo santykis labai didelis

        // Auksinio ženkliuko kriterijai
        if ($goodUnits >= 10000 && $soldUnits >= 5000 && $donatedUnits >= 5000 && $wasteRatio <= 0.10) {
            return 'badges/gold.png';
        }

        // Sidabrinio ženkliuko kriterijai
        if ($goodUnits >= 800 && $soldUnits >= 500 && $donatedUnits >= 300 && $wasteRatio <= 0.25) {
            return 'badges/metal.png';
        }

        // Bronzinio ženkliuko kriterijai
        if ($goodUnits >= 150 && $soldUnits >= 100 && $donatedUnits >= 50 && $wasteRatio <= 0.40) {
            return 'badges/bronze.png';
        }

        // Pradedančiojo / Be ženkliuko kriterijai
        // Jei gauta kokių nors "gerų" vienetų, bet nepakanka bronzai, arba visai nieko nedaryta.
        if ($goodUnits < 150) { // Pakeista, kad apimtų ir 0 'goodUnits'
            return 'badges/nobadge.png';
        }

        return null;
    }
}
