<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti (naudojama kuriant naują lentelę arba modifikuojant esamą)
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - prideda 'status' stulpelį į 'products' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     *
     * @return void
     */
    public function up(): void
    {
        // Naudojama Schema::table() metodas, nes modifikuojame jau egzistuojančią 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            $table->string('status')->default('sandelyje');
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'status' stulpelį iš 'products' lentelės.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Naudojama Schema::table() metodas, nes modifikuojame 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            // Pašalinamas 'status' stulpelis, jei jis egzistuoja.
            $table->dropColumn('status');
            // Jei $table->dropColumn('status'); nepavyktų
        });
    }
};
