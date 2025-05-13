<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

return new class extends Migration
{
    /**
     * Vykdo migracijas - prideda 'changed_by' stulpelį į 'product_changes' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     *
     * @return void
     */
    public function up() // Nurodytas metodas be grąžinimo tipo, bet veiks (Laravel 9+ rekomenduojama : void)
    {
        // Naudojama Schema::table() metodas, nes modifikuojame jau egzistuojančią 'product_changes' lentelę
        Schema::table('product_changes', function (Blueprint $table) {
            // Pridedamas naujas 'changed_by' stulpelis.
            $table->unsignedBigInteger('changed_by')->nullable();

        });
    }

    /**
     * Atšaukia migracijas - pašalina 'changed_by' stulpelį iš 'product_changes' lentelės.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down() // Nurodytas metodas be grąžinimo tipo, bet veiks (Laravel 9+ rekomenduojama : void)
    {
        // Naudojama Schema::table() metodas, nes modifikuojame 'product_changes' lentelę
        Schema::table('product_changes', function (Blueprint $table) {
            // Pašalinamas 'changed_by' stulpelis, jei jis egzistuoja.
            $table->dropColumn('changed_by');
        });
    }
};
