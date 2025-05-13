<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder; // Bazinė Seeder klasė

/**
 * @class DatabaseSeeder
 * @brief Pagrindinis duomenų bazės platinimas.
 *
 * Ši klasė yra kviečiama, kai paleidžiama `php artisan db:seed` komanda.
 * Jos `run()` metode galima kviesti kitus specifinius seeder'ius (pvz., UserSeeder, ProductSeeder)
 * arba tiesiogiai kurti pradinius duomenis aplikacijos lentelėse.
 * Tai naudinga pradiniam duomenų bazės užpildymui testavimo ar demonstraciniais duomenimis.
 *
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder // Paveldi iš bazinės Seeder klasės
{
    /**
     * Užpildo aplikacijos duomenų bazę pradinėmis sėklomis (duomenimis).
     * Šis metodas yra automatiškai kviečiamas, kai vykdoma `db:seed` komanda.
     *
     * @return void
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',          // Vartotojo vardas
            'email' => 'test@example.com', // Vartotojo el. paštas
            // Slaptažodis bus automatiškai sugeneruotas ir hešuotas pagal UserFactory logiką (dažniausiai 'password')
        ]);
    }
}
