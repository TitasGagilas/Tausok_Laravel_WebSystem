<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory; // Bazinė Eloquent gamyklos klasė
use Illuminate\Support\Facades\Hash; // Fasadas slaptažodžių hešavimui
use Illuminate\Support\Str; // Pagalbinė klasė darbui su eilutėmis

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 * Aukščiau esantis PHPDoc blokas nurodo, kad ši gamykla yra skirta \App\Models\User modeliui.
 * Tai padeda IDE geriau suprasti tipus.
 */
class UserFactory extends Factory // Paveldi iš bazinės Factory klasės
{
    /**
     * Dabartinis slaptažodis, naudojamas gamykloje.
     * Leidžia nustatyti tą patį hešuotą slaptažodį keliems vartotojams,
     * jei jis perduodamas per seeder'į ar testą.
     * Jei nenurodytas, bus sugeneruotas numatytasis 'password'.
     *
     * @var string|null
     */
    protected static ?string $password; // Statinė savybė slaptažodžiui laikyti

    /**
     * Apibrėžia modelio numatytąją būseną (default state).
     * Šis metodas grąžina masyvą su atributų reikšmėmis, kurios bus naudojamos
     * kuriant naują User modelio įrašą per šią gamyklą.
     *
     * @return array<string, mixed> Atributų ir jų reikšmių masyvas.
     */
    public function definition(): array
    {
        return [
            // 'name' atributui priskiriamas netikras vardas, sugeneruotas Faker bibliotekos pagalba
            'name' => fake()->name(),
            // 'email' atributui priskiriamas unikalus ir saugus netikras el. pašto adresas
            'email' => fake()->unique()->safeEmail(),
            // 'email_verified_at' atributui priskiriama dabartinė data ir laikas (vartotojas laikomas patvirtintu)
            'email_verified_at' => now(),
            // 'password' atributui priskiriamas arba anksčiau nustatytas statinis $password,
            // arba naujai suhešuota reikšmė 'password'.
            // `??=` yra null coalescing assignment operatorius: jei static::$password yra null,
            // jam priskiriama Hash::make('password') reikšmė.
            'password' => static::$password ??= Hash::make('password'),
            // 'remember_token' atributui priskiriama atsitiktinė 10 simbolių ilgio eilutė
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Nurodo, kad modelio el. pašto adresas turėtų būti nepatvirtintas.
     * Tai yra būsenos (state) metodas, leidžiantis modifikuoti numatytąją gamyklos būseną.
     * Naudojamas, pvz., UserFactory::new()->unverified()->create();
     *
     * @return static Grąžina pačios gamyklos instanciją, leidžiant toliau grandyti metodus.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
