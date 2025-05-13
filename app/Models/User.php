<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @class User
 * @brief Eloquent modelis, atitinkantis `users` lentelę duomenų bazėje.
 *
 * Šis modelis reprezentuoja aplikacijos vartotoją ir apibrėžia jo savybes,
 * masiniam priskyrimui leidžiamus laukus, paslėptus laukus serializuojant
 * ir atributų tipų konvertavimą. Jis paveldi iš Authenticatable klasės,
 * kas suteikia visą reikiamą funkcionalumą vartotojų autentifikavimui.
 *
 * @package App\Models
 */
class User extends Authenticatable // Paveldi iš Authenticatable, ne tik iš Model
{
    // HasFactory leidžia naudoti gamyklas testavimui ir duomenų generavimui.
    // Notifiable leidžia vartotojui gauti Laravel pranešimus (pvz., el. paštu).
    /** @use HasFactory<\Database\Factories\UserFactory> */ // PHPDoc blokas tipui nurodyti (generuojamas automatiškai)
    use HasFactory, Notifiable;

    /**
     * Atributai, kuriuos leidžiama masiškai priskirti (mass assignable).
     * Svarbu saugumui, kad nebūtų galima priskirti reikšmių jautriems laukams.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',     // Vartotojo vardas
        'email',    // Vartotojo el. pašto adresas
        'password', // Vartotojo slaptažodis (bus automatiškai hešuojamas per $casts)
    ];

    /**
     * Atributai, kurie turėtų būti paslėpti serializuojant modelį.
     * Dažniausiai naudojama tam, kad slaptažodis ir "prisiminti mane" žetonas
     * nebūtų grąžinami, pvz., konvertuojant modelį į JSON API atsakymui.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',         // Slaptažodis
        'remember_token',   // "Prisiminti mane" žetonas
    ];

    /**
     * Metodas, grąžinantis atributų tipų konvertavimo (casting) taisykles.
     * Leidžia nurodyti, kaip tam tikri atributai turėtų būti automatiškai
     * konvertuojami į nurodytus PHP tipus, kai jie gaunami iš DB arba įrašomi.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Naudojama, jei įjungtas el. pašto patvirtinimas.
            'email_verified_at' => 'datetime',
            // 'password' laukas bus automatiškai hešuojamas (hashed) prieš įrašant į duomenų bazę.
            'password' => 'hashed',
        ];
    }
}
