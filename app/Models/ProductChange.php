<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @class ProductChange
 * @brief Eloquent modelis, atitinkantis `product_changes` lentelę duomenų bazėje.
 *
 * Šis modelis naudojamas registruoti individualius produktų atributų pakeitimus,
 * tokius kaip pavadinimo, aprašymo, kainos ir t.t. pakeitimai.
 * Tai leidžia sekti, kas, kada ir kokį produkto lauką pakeitė.
 *
 * @package App\Models
 */
class ProductChange extends Model
{
    /**
     * Laukai, kuriuos leidžiama masiškai priskirti (mass assignable).
     * Nurodo, kuriuos stulpelius galima užpildyti naudojant ProductChange::create() ar panašius metodus.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',   // ID produkto, kuriam priklauso šis pakeitimas
        'field',        // Pakeisto lauko pavadinimas
        'old_value',    // Sena lauko reikšmė prieš pakeitimą
        'new_value',    // Nauja lauko reikšmė po pakeitimo
        'changed_by',   // Vartotojo ID, kuris atliko pakeitimą
        'changed_at',   // Pakeitimo data ir laikas
    ];

    /**
     * Atributų tipų konvertavimas (casting).
     * Nurodo Laravel, kaip tam tikri atributai turėtų būti automatiškai konvertuojami.
     *
     * @var array
     */
    protected $casts = [
        // 'changed_at' laukas bus automatiškai konvertuojamas į Carbon datos objektą.
        'changed_at' => 'datetime',
    ];

    /**
     * Apibrėžia "priklauso vienam" (belongsTo) ryšį su Product modeliu.
     * Nurodo, kad kiekvienas produkto pakeitimo įrašas priklauso vienam produktui.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo // Tipas nurodytas dėl aiškumo
    {
        // product_id yra numanomas išorinis raktas, jei nenurodyta kitaip
        return $this->belongsTo(Product::class);
    }

    /**
     * Apibrėžia "priklauso vienam" (belongsTo) ryšį su User modeliu.
     * Nurodo, kad kiekvieną produkto pakeitimą atliko vienas vartotojas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function changer(): BelongsTo // Metodo pavadinimas 'changer' (kas pakeitė)
    {
        // Kadangi išorinis raktas vadinasi 'changed_by', o ne numanomas 'user_id',
        // jį reikia nurodyti kaip antrą argumentą belongsTo metode.
        return $this->belongsTo(User::class, 'changed_by');
    }
}
