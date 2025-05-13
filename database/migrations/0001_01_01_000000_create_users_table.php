<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda.
     *
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'users' lentelę su nurodytais stulpeliais
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Sukuria automatiškai didėjantį pirminį raktą 'id' (bigint unsigned)
            $table->string('name'); // Sukuria 'name' stulpelį (varchar) vartotojo vardui
            $table->string('email')->unique(); // Sukuria 'email' stulpelį (varchar) el. paštui, reikšmės turi būti unikalios
            $table->timestamp('email_verified_at')->nullable(); // Sukuria 'email_verified_at' stulpelį (timestamp) el. pašto patvirtinimo laikui. Leidžiama NULL reikšmė (neprivalomas).
            $table->string('password'); // Sukuria 'password' stulpelį (varchar) slaptažodžiui (bus saugomas hešuotas)
            $table->rememberToken(); // Sukuria 'remember_token' stulpelį (varchar(100), nullable) "prisiminti mane" funkcijai
            $table->timestamps(); // Sukuria 'created_at' ir 'updated_at' stulpelius (timestamp, nullable) įrašo sukūrimo ir atnaujinimo laikui sekti
        });

        // Sukuria 'password_reset_tokens' lentelę slaptažodžio atstatymo žetonams saugoti
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // 'email' stulpelis yra pirminis raktas šioje lentelėje ir taip pat indeksuotas.
            // Nurodo vartotojo el. paštą, kuriam skirtas atstatymo žetonas.
            $table->string('email')->primary();
            $table->string('token'); // Slaptažodžio atstatymo žetonas (token)
            $table->timestamp('created_at')->nullable(); // Žetono sukūrimo laikas (gali būti null)
        });

        // Sukuria 'sessions' lentelę vartotojų sesijų informacijai saugoti
        // Tai naudojama, jei sesijų tvarkyklė (session driver) nustatyta į 'database'.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Sesijos ID (pirminis raktas)
            // Vartotojo ID, susietas su sesija. Gali būti null (pvz., svečio sesija). Indeksuotas.
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable(); // Vartotojo IP adresas (varchar(45))
            $table->text('user_agent')->nullable(); // Vartotojo naršyklės informacija (user agent string)
            $table->longText('payload'); // Serializuoti sesijos duomenys
            $table->integer('last_activity')->index(); // Paskutinės vartotojo veiklos laikas (timestamp kaip integer). Indeksuotas.
        });
    }

    /**
     * Atšaukia migracijas - pašalina sukurtas lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'users' lentelę, jei ji egzistuoja
        Schema::dropIfExists('users');
        // Pašalina 'password_reset_tokens' lentelę, jei ji egzistuoja
        Schema::dropIfExists('password_reset_tokens');
        // Pašalina 'sessions' lentelę, jei ji egzistuoja
        Schema::dropIfExists('sessions');
    }
};
