<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

return new class extends Migration
{
    /**
     * Vykdo migracijas - prideda naujus kiekių stulpelius ('sold_qty', 'wasted_qty', 'donated_qty')
     * į 'products' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     * Šie stulpeliai skirti saugoti suminius parduotų, išmestų ir paaukotų
     * produkto vienetų kiekius tiesiogiai produkto įraše.
     *
     * @return void
     */
    public function up()
    {
        // Naudojama Schema::table() metodas, nes modifikuojame jau egzistuojančią 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            // Pridedamas 'sold_qty' stulpelis parduotų vienetų kiekiui saugoti.
            // unsignedInteger reiškia teigiamą sveikąjį skaičių.
            // default(0) nustato numatytąją reikšmę 0.
            $table->unsignedInteger('sold_qty')->default(0)->comment('Bendras parduotas šio produkto kiekis (vienetais)');

            // Pridedamas 'wasted_qty' stulpelis išmestų vienetų kiekiui saugoti.
            $table->unsignedInteger('wasted_qty')->default(0)->comment('Bendras išmestas šio produkto kiekis (vienetais)');

            // Pridedamas 'donated_qty' stulpelis paaukotų vienetų kiekiui saugoti.
            $table->unsignedInteger('donated_qty')->default(0)->comment('Bendras paaukotas šio produkto kiekis (vienetais)');

        });
    }

    /**
     * Atšaukia migracijas - pašalina 'sold_qty', 'wasted_qty', 'donated_qty' stulpelius
     * iš 'products' lentelės.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down() // Laravel 9+ rekomenduojama : void grąžinimo tipas
    {
        // Naudojama Schema::table() metodas, nes modifikuojame 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            // Pašalinami pridėti stulpeliai. dropColumn metodui galima perduoti masyvą su stulpelių pavadinimais.
            $table->dropColumn(['sold_qty', 'wasted_qty', 'donated_qty']);
        });
    }
};
