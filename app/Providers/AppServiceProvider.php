<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * @class AppServiceProvider
 * @brief Pagrindinis aplikacijos servisų teikėjas.
 *
 * Šis servisų teikėjas yra įkeliamas kiekvienos užklausos metu ir naudojamas
 * bazinėms aplikacijos paslaugoms registruoti, pradiniams nustatymams (bootstrap) atlikti
 * arba kitiems bendriems veiksmams, kurie turi būti atlikti aplikacijos starto metu.
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registruoja bet kokias aplikacijos paslaugas (services).
     *
     * Šis metodas yra kviečiamas, kai Laravel registruoja visus servisų teikėjus.
     * Tai leidžia lengvai valdyti priklausomybes (dependencies) visoje aplikacijoje.
     *
     *
     * @return void
     */
    public function register(): void
    {
        // Jei ateityje prireiktų registruoti nuosavus servisus ar sąsajas,
        // tai būtų daroma čia.
    }

    /**
     * Atlieka pradinius aplikacijos nustatymus (bootstrap).
     *
     * Šis metodas yra kviečiamas po to, kai visi kiti servisų teikėjai
     * jau yra užregistruoti.
     *
     * @return void
     */
    public function boot(): void
    {

    }
}
