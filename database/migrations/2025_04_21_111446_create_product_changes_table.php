<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria 'product_changes' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     * Lentelė skirta saugoti produktų laukų pakeitimų istoriją (audit log).
     *
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'product_changes' lentelę su nurodytais stulpeliais
        Schema::create('product_changes', function (Blueprint $table) {
            $table->id(); // Automatiškai didėjantis pirminis raktas 'id' (bigint unsigned)

            // 'product_id' stulpelis: išorinis raktas, susietas su 'id' stulpeliu 'products' lentelėje.
            // onDelete('cascade') reiškia, kad jei produktas bus ištrintas,
            // visi susiję pakeitimų įrašai taip pat bus automatiškai ištrinti.
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // 'field' stulpelis: pakeisto produkto lauko pavadinimas (string), pvz., 'name', 'price', 'quantity'.
            $table->string('field');

            // 'old_value' stulpelis: buvusi lauko reikšmė prieš pakeitimą (string).
            // Leidžiama NULL reikšmė, jei laukas anksčiau neturėjo reikšmės.
            $table->string('old_value')->nullable();

            // 'new_value' stulpelis: nauja lauko reikšmė po pakeitimo (string).
            // Leidžiama NULL reikšmė, jei nauja reikšmė yra null.
            $table->string('new_value')->nullable();

            $table->timestamps(); // Sukuria 'created_at' ir 'updated_at' stulpelius (timestamp)
            // įrašo sukūrimo ir paskutinio atnaujinimo laikui sekti.
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'product_changes' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'product_changes' lentelę, jei ji egzistuoja
        Schema::dropIfExists('product_changes');
    }
};
