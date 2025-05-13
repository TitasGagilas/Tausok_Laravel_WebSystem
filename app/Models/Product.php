<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // Bazinė Eloquent modelio klasė
use Illuminate\Database\Eloquent\Factories\HasFactory; // Leidžia naudoti modelių gamyklas testavimui/duomenų užpildymui
use Illuminate\Database\Eloquent\Relations\HasMany; // Tipas "vienas su daugeliu" ryšiui

/**
 * @class Product
 * @brief Eloquent modelis, atitinkantis `products` lentelę duomenų bazėje.
 *
 * Šis modelis apibrėžia produkto duomenų struktūrą, leidžiamus masiniam priskyrimui laukus (fillable),
 * duomenų tipų konvertavimą (casts) ir ryšius su kitais modeliais (User, ProductChange, ProductTransaction).
 * Taip pat turi aksesuorius (accessors) apskaičiuotiems kiekiams gauti (parduota, paaukota ir t.t.).
 *
 * @package App\Models
 */
class Product extends Model
{
    // Naudojama HasFactory savybė, leidžianti kurti modelio objektus per gamyklas (factories)
    use HasFactory;

    /**
     * Laukai, kuriuos leidžiama masiškai priskirti (mass assignable).
     * Tai apsaugo nuo nepageidaujamų laukų atnaujinimo per, pvz., Product::create() ar $product->update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',          // Vartotojo ID, kuriam priklauso produktas
        'name',             // Produkto pavadinimas
        'description',      // Produkto aprašymas
        'image',            // Kelias iki produkto paveikslėlio
        'expiration_date',  // Produkto galiojimo data
        'quantity',         // Dabartinis produkto likutis sandėlyje (vienetais)
        'initial_quantity', // Pradinis produkto kiekis (vienetais), įvestas kuriant produktą
        'weight',           // Produkto vieneto svoris (pagal dabartinę logiką tai gali būti eilutė, pvz., "500g")
        'price',            // Produkto vieneto kaina
        'status',           // Produkto statusas (pvz., 'Sandelyje', 'Parduota')
    ];

    /**
     * Nurodo ryšį su User modeliu (produktas priklauso vartotojui).
     * 'belongsTo' reiškia "priklauso vienam".
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Nurodomas User modelis ir automatiškai ieškomas user_id stulpelis
    }

    /**
     * Nurodo ryšį su ProductChange modeliu (produktas gali turėti daug pakeitimų įrašų).
     * 'hasMany' reiškia "turi daug".
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changes(): HasMany // Tipas nurodytas dėl aiškumo
    {
        return $this->hasMany(\App\Models\ProductChange::class); // Nurodomas ProductChange modelis
    }

    /**
     * Nurodo ryšį su ProductTransaction modeliu (produktas gali turėti daug transakcijų).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(ProductTransaction::class); // Nurodomas ProductTransaction modelis
    }

    // --- Aksesoriai (Accessors) Apskaičiuotiems Kiekiams ---
    // Aksesuoriai leidžia apibrėžti virtualius atributus modelyje,
    // kurie yra apskaičiuojami pagal kitus duomenis.
    // Jie kviečiami kaip paprasti modelio atributai, pvz., $product->sold_quantity.

    /**
     * Aksesorius, apskaičiuojantis bendrą parduotų šio produkto vienetų kiekį.
     * Sumuoja 'quantity' iš visų 'Parduota' tipo transakcijų, susijusių su šiuo produktu.
     *
     * @return int Bendras parduotas kiekis.
     */
    public function getSoldQuantityAttribute(): int
    {
        // Kreipiamasi į 'transactions' ryšį, filtruojama pagal 'action_type' ir sumuojamas 'quantity'
        return (int) $this->transactions()->where('action_type', 'Parduota')->sum('quantity');
    }

    /**
     * Aksesorius, apskaičiuojantis bendrą paaukotų šio produkto vienetų kiekį.
     *
     * @return int Bendras paaukotas kiekis.
     */
    public function getDonatedQuantityAttribute(): int
    {
        return (int) $this->transactions()->where('action_type', 'Paaukota')->sum('quantity');
    }

    /**
     * Aksesorius, apskaičiuojantis bendrą išmestų šio produkto vienetų kiekį.
     *
     * @return int Bendras išmestas kiekis.
     */
    public function getWastedQuantityAttribute(): int
    {
        return (int) $this->transactions()->where('action_type', 'Išmesta')->sum('quantity');
    }

    /**
     * Aksesorius, apskaičiuojantis bendrą rezervuotų šio produkto vienetų kiekį.
     *
     * @return int Bendras rezervuotas kiekis.
     */
    public function getReservedQuantityAttribute(): int
    {
        return (int) $this->transactions()->where('action_type', 'Rezervuota')->sum('quantity');
    }

    /**
     * Atributų tipų konvertavimas (casting).
     * Nurodo Laravel, kaip tam tikri atributai turėtų būti automatiškai konvertuojami
     * gaunant juos iš duomenų bazės arba įrašant į ją.
     *
     * @var array
     */
    protected $casts = [
        'expiration_date' => 'date', // 'expiration_date' bus automatiškai konvertuojamas į Carbon datos objektą
    ];
}
