<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria 'cache' ir 'cache_locks' lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'cache' lentelę duomenims kešuoti
        Schema::create('cache', function (Blueprint $table) {
            // 'key' stulpelis: unikalus kešo įrašo raktas (string). Tai yra pirminis raktas.
            $table->string('key')->primary();
            // 'value' stulpelis: pati kešuota reikšmė (mediumText tipas, kad tilptų daugiau duomenų).
            $table->mediumText('value');
            // 'expiration' stulpelis: kešo įrašo galiojimo laikas (integer, Unix timestamp).
            // Nurodo, kada įrašas laikomas pasibaigusiu ir nebegaliojančiu.
            $table->integer('expiration');
        });

        // Sukuria 'cache_locks' lentelę atominiams kešo užraktams (locks) valdyti.
        // Tai padeda išvengti "race conditions", kai keli procesai bando vienu metu
        // modifikuoti tą patį kešo įrašą.
        Schema::create('cache_locks', function (Blueprint $table) {
            // 'key' stulpelis: unikalus užrakto raktas (string). Tai yra pirminis raktas.
            $table->string('key')->primary();
            // 'owner' stulpelis: unikalus identifikatorius proceso, kuris šiuo metu laiko užraktą (string).
            $table->string('owner');
            // 'expiration' stulpelis: užrakto galiojimo laikas (integer, Unix timestamp).
            // Nurodo, kada užraktas automatiškai nustos galioti.
            $table->integer('expiration');
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'cache' ir 'cache_locks' lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'cache' lentelę, jei ji egzistuoja
        Schema::dropIfExists('cache');
        // Pašalina 'cache_locks' lentelę, jei ji egzistuoja
        Schema::dropIfExists('cache_locks');
    }
};
