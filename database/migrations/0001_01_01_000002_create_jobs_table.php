<?php

use Illuminate\Database\Migrations\Migration; // Bazinė migracijų klasė
use Illuminate\Database\Schema\Blueprint;    // Klasė lentelės struktūrai apibrėžti
use Illuminate\Support\Facades\Schema;     // Fasadas darbui su duomenų bazės schema

// Grąžinama anoniminė klasė, paveldinti iš Migration.
return new class extends Migration
{
    /**
     * Vykdo migracijas - sukuria 'jobs', 'job_batches' ir 'failed_jobs' lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate` komanda,
     * @return void
     */
    public function up(): void
    {
        // Sukuria 'jobs' lentelę pagrindinėms eilės užduotims (jobs) saugoti
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // Automatiškai didėjantis pirminis raktas 'id' (bigint unsigned)
            // 'queue' stulpelis: eilės pavadinimas, kuriai priklauso ši užduotis (string). Indeksuotas.
            // Leidžia turėti kelias eiles skirtingoms užduotims (pvz., 'emails', 'reports').
            $table->string('queue')->index();
            // 'payload' stulpelis: serializuoti užduoties duomenys (longText), įskaitant pačią užduoties klasę ir jos savybes.
            $table->longText('payload');
            // 'attempts' stulpelis: kiek kartų buvo bandyta įvykdyti šią užduotį (unsignedTinyInteger).
            $table->unsignedTinyInteger('attempts');
            // 'reserved_at' stulpelis: laiko žymė (timestamp kaip unsignedInteger), kada "worker" procesas paėmė šią užduotį vykdymui.
            // Gali būti null, jei užduotis dar nepaimta.
            $table->unsignedInteger('reserved_at')->nullable();
            // 'available_at' stulpelis: laiko žymė (timestamp kaip unsignedInteger), kada ši užduotis taps prieinama vykdymui.
            // Naudojama atidėtoms užduotims (delayed jobs).
            $table->unsignedInteger('available_at');
            // 'created_at' stulpelis: laiko žymė (timestamp kaip unsignedInteger), kada užduotis buvo įdėta į eilę.
            $table->unsignedInteger('created_at');
        });

        // Sukuria 'job_batches' lentelę užduočių paketams (job batches) sekti.
        // Užduočių paketai leidžia grupuoti kelias užduotis ir stebėti jų bendrą vykdymo būseną.
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // Unikalus paketo ID (string, pirminis raktas).
            $table->string('name'); // Paketo pavadinimas (pvz., 'Importuoti Vartotojus').
            $table->integer('total_jobs');     // Bendras užduočių skaičius šiame pakete.
            $table->integer('pending_jobs');   // Laukiančių (dar nepradėtų) užduočių skaičius.
            $table->integer('failed_jobs');    // Nepavykusių užduočių skaičius šiame pakete.
            $table->longText('failed_job_ids'); // Serializuotas nepavykusių užduočių ID sąrašas.
            $table->mediumText('options')->nullable(); // Papildomos paketo parinktys (serializuotos, gali būti null).
            $table->integer('cancelled_at')->nullable(); // Laiko žymė (timestamp), kada paketas buvo atšauktas (gali būti null).
            $table->integer('created_at');             // Paketo sukūrimo laiko žymė (timestamp).
            $table->integer('finished_at')->nullable();  // Laiko žymė (timestamp), kada visos paketo užduotys baigtos (gali būti null).
        });

        // Sukuria 'failed_jobs' lentelę nepavykusioms eilės užduotims saugoti.
        // Kai užduotis nepavyksta (pvz., įvyksta klaida vykdymo metu), Laravel ją įrašo į šią lentelę
        // tolimesnei analizei ar bandymui paleisti iš naujo.
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // Automatiškai didėjantis pirminis raktas 'id'.
            // 'uuid' stulpelis: unikalus nepavykusios užduoties identifikatorius (string). Unikalus.
            // Leidžia identifikuoti konkrečią nepavykusią užduotį, net jei ji buvo bandyta kelis kartus.
            $table->string('uuid')->unique();
            $table->text('connection'); // Eilės prisijungimo pavadinimas, per kurį buvo siunčiama užduotis.
            $table->text('queue');      // Eilės pavadinimas, kuriai priklausė užduotis.
            $table->longText('payload');  // Serializuoti nepavykusios užduoties duomenys.
            $table->longText('exception'); // Serializuota išimtis (exception), dėl kurios užduotis nepavyko.
            // 'failed_at' stulpelis: laiko žymė (timestamp), kada užduotis nepavyko. Pagal nutylėjimą naudoja dabartinį laiką.
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Atšaukia migracijas - pašalina 'jobs', 'job_batches' ir 'failed_jobs' lenteles.
     * Šis metodas kviečiamas, kai paleidžiama `php artisan migrate:rollback` komanda.
     *
     * @return void
     */
    public function down(): void
    {
        // Pašalina 'jobs' lentelę, jei ji egzistuoja
        Schema::dropIfExists('jobs');
        // Pašalina 'job_batches' lentelę, jei ji egzistuoja
        Schema::dropIfExists('job_batches');
        // Pašalina 'failed_jobs' lentelę, jei ji egzistuoja
        Schema::dropIfExists('failed_jobs');
    }
};
