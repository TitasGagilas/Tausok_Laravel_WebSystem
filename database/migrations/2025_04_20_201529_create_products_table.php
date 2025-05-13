<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria 'products' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     *
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'products' lentelę su nurodytais stulpeliais
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Sukuria automatiškai didėjantį pirminį raktą 'id' (bigint unsigned)

            // Sukuria 'user_id' stulpelį (unsignedBigInteger) ir nustato jį kaip išorinį raktą (foreign key),
            // susietą su 'id' stulpeliu 'users' lentelėje.
            // onDelete('cascade') reiškia, kad jei vartotojas bus ištrintas, visi jo produktai taip pat bus automatiškai ištrinti.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name'); // Sukuria 'name' stulpelį (varchar) produkto pavadinimui
            $table->text('description'); // Sukuria 'description' stulpelį (text tipo) produkto aprašymui (gali talpinti daugiau teksto nei varchar)
            $table->string('image')->nullable(); // Sukuria 'image' stulpelį (varchar) kelio iki produkto paveikslėlio saugojimui. Leidžiama NULL reikšmė (paveikslėlis neprivalomas).
            $table->date('expiration_date'); // Sukuria 'expiration_date' stulpelį (date tipo) produkto galiojimo datai
            $table->integer('quantity'); // Sukuria 'quantity' stulpelį (integer tipo) dabartiniam produkto kiekiui sandėlyje

            $table->string('weight'); // Sukuria 'weight' stulpelį (varchar) produkto svoriui. Pagal ankstesnes diskusijas, tai turėtų būti decimal.

            $table->decimal('price', 8, 2); // Sukuria 'price' stulpelį (decimal tipo) produkto kainai.
            // 8 skaitmenys iš viso, 2 po kablelio (pvz., 123456.78).

            $table->timestamps(); // Sukuria 'created_at' ir 'updated_at' stulpelius (timestamp, nullable)
            // įrašo sukūrimo ir paskutinio atnaujinimo laikui sekti.
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'products' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'products' lentelę, jei ji egzistuoja
        Schema::dropIfExists('products');
    }
};
