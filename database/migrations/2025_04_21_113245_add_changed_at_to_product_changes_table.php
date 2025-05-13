<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - prideda 'changed_by' stulpelį į 'product_changes' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     * Šis stulpelis skirtas saugoti vartotojo, atlikusio pakeitimą, ID.
     *
     * @return void
     */
    public function up() // Laravel 9+ rekomenduojama : void grąžinimo tipas
    {
        // Naudojama Schema::table() metodas, nes modifikuojame jau egzistuojančią 'product_changes' lentelę
        Schema::table('product_changes', function (Blueprint $table) {
            // Pridedamas naujas 'changed_by' stulpelis.
            // unsignedBigInteger yra standartinis tipas išoriniams raktams, rodantiems į 'id' stulpelius (pvz., users.id).
            // ->nullable() leidžia šiam laukui turėti NULL reikšmę, jei, pavyzdžiui,
            $table->unsignedBigInteger('changed_by')->nullable()->after('new_value');

        });
    }

    /**
     * Atšaukia migracijas - pašalina 'changed_by' stulpelį iš 'product_changes' lentelės.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down() // Laravel 9+ rekomenduojama : void grąžinimo tipas
    {
        // Naudojama Schema::table() metodas, nes modifikuojame 'product_changes' lentelę
        Schema::table('product_changes', function (Blueprint $table) {

            // Pašalinamas 'changed_by' stulpelis.
            $table->dropColumn('changed_by');
        });
    }
};
