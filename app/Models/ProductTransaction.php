<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @class ProductTransaction
 * @brief Eloquent modelis, atitinkantis `product_transactions` lentelę duomenų bazėje.
 *
 * Šis modelis saugo įrašus apie visus produktų kiekių pasikeitimus sistemoje,
 * tokius kaip pardavimai, aukojimai, nurašymai, rezervacijos, pradiniai gavimai
 * ar kiekio korekcijos. Kiekviena transakcija yra susieta su konkrečiu produktu ir vartotoju.
 *
 * @package App\Models
 */
class ProductTransaction extends Model
{
    use HasFactory;

    /**
     * Laukai, kuriuos leidžiama masiškai priskirti (mass assignable).
     * Nurodo, kuriuos stulpelius galima užpildyti naudojant ProductTransaction::create()
     * ar panašius metodus.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',       // Produkto ID, su kuriuo susijusi ši transakcija
        'user_id',          // Vartotojo ID, kuris atliko šią transakciją
        'action_type',      // Veiksmo tipas (pvz., 'Parduota', 'Paaukota', 'Išmesta', 'Rezervuota', 'Gauta', 'Korekcija')
        'quantity',         // Kiekis, susijęs su šiuo veiksmu (gali būti teigiamas arba neigiamas, priklausomai nuo logikos)
        'transaction_date', // Data, kada įvyko transakcija (gali būti vartotojo nurodyta praeities data)
        'notes',            // Papildomos pastabos apie transakciją (pvz., korekcijos priežastis)
    ];


    /**
     * Atributų tipų konvertavimas (casting).
     * Nurodo Laravel, kaip tam tikri atributai turėtų būti automatiškai konvertuojami.
     *
     * @var array
     */
    protected $casts = [
        // 'transaction_date' laukas bus automatiškai konvertuojamas į Carbon datos objektą.
        'transaction_date' => 'datetime',
    ];

    /**
     * Apibrėžia "priklauso vienam" (belongsTo) ryšį su Product modeliu.
     * Nurodo, kad kiekviena transakcija priklauso vienam produktui.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        // product_id yra numanomas išorinis raktas, jei nenurodyta kitaip
        return $this->belongsTo(Product::class);
    }

    /**
     * Apibrėžia "priklauso vienam" (belongsTo) ryšį su User modeliu.
     * Nurodo, kad kiekvieną transakciją atliko vienas vartotojas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // user_id yra numanomas išorinis raktas
        return $this->belongsTo(User::class);
    }
}
