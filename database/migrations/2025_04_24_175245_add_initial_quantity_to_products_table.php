<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema
use App\Models\Product;                   // Importuojamas Product modelis duomenų atnaujinimui

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - prideda 'initial_quantity' stulpelį į 'products' lentelę
     * ir bando užpildyti šį stulpelį esamiems įrašams.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     *
     * @return void
     */
    public function up(): void
    {
        // Naudojama Schema::table() metodas, nes modifikuojame jau egzistuojančią 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            // Pridedamas naujas 'initial_quantity' stulpelis (unsignedInteger tipo).
            // 'unsigned()' reiškia, kad reikšmės bus tik teigiamos arba 0.
            // 'default(0)' nustato numatytąją reikšmę 0 naujai kuriamiems įrašams
            $table->integer('initial_quantity')->unsigned()->default(0)->after('quantity')->comment('Pradinis produkto kiekis, įvestas jį sukuriant ar redaguojant');
        });

        // --- Esamų Duomenų Atnaujinimas ---
        // Ši dalis bando nustatyti 'initial_quantity' reikšmę esamiems produktams,
        // prilyginant ją dabartinei 'quantity' reikšmei.

        foreach (Product::cursor() as $product) {
            // `updateQuietly` atnaujina modelį NEIŠKVIEČIANT modelio įvykių (events)
            // tokių kaip 'saving', 'saved', 'updating', 'updated'.
            $product->updateQuietly(['initial_quantity' => $product->quantity]);
        }
        // --- Esamų Duomenų Atnaujinimo Pabaiga ---
    }

    /**
     * Atšaukia migracijas - pašalina 'initial_quantity' stulpelį iš 'products' lentelės.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Naudojama Schema::table() metodas, nes modifikuojame 'products' lentelę
        Schema::table('products', function (Blueprint $table) {
            // Pašalinamas 'initial_quantity' stulpelis.
            $table->dropColumn('initial_quantity');
        });
    }
};
