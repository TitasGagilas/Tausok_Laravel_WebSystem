<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria 'product_transactions' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     * Ši lentelė skirta saugoti visus įrašus apie produktų kiekių pokyčius,
     * tokius kaip gavimas, pardavimas, aukojimas, nurašymas, rezervacija ir kt.
     *
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'product_transactions' lentelę su nurodytais stulpeliais
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id(); // Automatiškai didėjantis pirminis raktas 'id' (bigint unsigned)

            // 'product_id' stulpelis: išorinis raktas, susietas su 'id' stulpeliu 'products' lentelėje.
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Link to products table

            // 'user_id' stulpelis: išorinis raktas, susietas su 'id' stulpeliu 'users' lentelėje.
            // Nurodo vartotoją, kuris atliko arba su kuriuo susijusi ši transakcija.
            // `onDelete('cascade')` taip pat taikomas čia (jei vartotojas ištrinamas, jo transakcijos irgi).
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to users table (who performed action)

            // 'action_type' stulpelis: transakcijos tipo pavadinimas (string), pvz.,
            // 'Gauta', 'Parduota', 'Paaukota', 'Išmesta', 'Rezervuota', 'Korekcija' ir pan.
            $table->string('action_type');

            // 'quantity' stulpelis: produkto vienetų skaičius, susijęs su šia transakcija (integer).
            // Šis kiekis gali būti teigiamas (pvz., 'Gauta', 'Korekcija+') arba
            // neigiamas (pvz., 'Parduota', 'Išmesta', 'Korekcija-'), priklausomai nuo to,
            $table->integer('quantity');

            // 'transaction_date' stulpelis: data ir laikas (timestamp), kada įvyko transakcija.
            // `useCurrent()` nustatytų numatytąją reikšmę į dabartinį laiką kuriant įrašą,
            $table->timestamp('transaction_date')->useCurrent(); // Nustato numatytąją reikšmę į dabartinį laiką

            // 'notes' stulpelis: papildomos pastabos ar komentarai apie transakciją (text tipo).
            // Leidžiama NULL reikšmė (neprivalomas).
            $table->text('notes')->nullable();

            $table->timestamps(); // Sukuria 'created_at' ir 'updated_at' stulpelius (timestamp)
            // įrašo sukūrimo ir paskutinio atnaujinimo laikui sekti.
            // 'created_at' čia fiksuos, kada įrašas buvo sukurtas DB,
            // o 'transaction_date' - kada realiai įvyko operacija.
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'product_transactions' lentelę.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'product_transactions' lentelę, jei ji egzistuoja
        Schema::dropIfExists('product_transactions');
    }
};
