<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @class LoginRequest
 * @brief Formos užklausos klasė vartotojo prisijungimui.
 *
 * Atsakinga už prisijungimo formos duomenų validavimą, bandymą autentifikuoti vartotoją
 * ir bandymų skaičiaus ribojimą (rate limiting), siekiant apsisaugoti nuo
 * "brute-force" atakų.
 *
 * @package App\Http\Requests\Auth
 */
class LoginRequest extends FormRequest
{
    /**
     * Nustato, ar vartotojas yra autorizuotas atlikti šią užklausą.
     * Prisijungimo užklausai paprastai visi (net ir neautentifikuoti) vartotojai
     * yra autorizuoti ją pateikti.
     *
     * @return bool Visada grąžina true, leidžiant visiems bandyti jungtis.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Grąžina validacijos taisykles, taikomas šiai prisijungimo užklausai.
     * Apibrėžia, kad 'email' ir 'password' laukai yra privalomi ir turi būti eilutės.
     * 'email' taip pat turi atitikti el. pašto formatą.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Validacijos taisyklių masyvas.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'], // El. paštas: privalomas, eilutė, el. pašto formatas
            'password' => ['required', 'string'],      // Slaptažodis: privalomas, eilutė
        ];
    }

    /**
     * Bando autentifikuoti vartotoją pagal pateiktus prisijungimo duomenis.
     * Prieš bandant autentifikuoti, patikrina, ar neviršytas bandymų limitas.
     * Jei autentifikacija sėkminga, išvalo bandymų limitą.
     * Jei nesėkminga, padidina bandymų skaitiklį ir grąžina validacijos klaidą.
     *
     * @throws \Illuminate\Validation\ValidationException Jei autentifikacija nepavyksta arba viršytas bandymų limitas.
     */
    public function authenticate(): void
    {
        // Užtikrina, kad užklausa nėra apribota dėl per daug bandymų
        $this->ensureIsNotRateLimited();

        // Bando autentifikuoti vartotoją naudojant pateiktą el. paštą ir slaptažodį.
        // $this->boolean('remember') patikrina, ar formoje buvo pažymėtas "Prisiminti mane" laukelis.
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Jei autentifikacija nepavyko, padidinamas bandymų skaitiklis šiam vartotojui/IP
            RateLimiter::hit($this->throttleKey());

            // Grąžinama validacijos klaida su pranešimu iš lokalizacijos failo 'auth.failed'
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // Standartinis pranešimas "Prisijungimo duomenys neatitinka."
            ]);
        }

        // Jei autentifikacija sėkminga, išvalomas bandymų skaitiklis šiam vartotojui/IP
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Užtikrina, kad prisijungimo užklausa nėra apribota dėl per daug bandymų.
     * Jei bandymų limitas viršytas, iškviečiamas Lockout įvykis ir grąžinama
     * validacijos klaida su informacija, po kiek laiko bus galima bandyti vėl.
     *
     * @throws \Illuminate\Validation\ValidationException Jei viršytas bandymų limitas.
     */
    public function ensureIsNotRateLimited(): void
    {
        // Patikrina, ar neviršytas bandymų limitas (numatytoji reikšmė: 5 bandymai per minutę Laravel Breeze)
        // $this->throttleKey() generuoja unikalų raktą bandymų ribojimui.
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) { // 5 bandymai
            return; // Jei limitas neviršytas, metodas baigia darbą
        }

        // Jei limitas viršytas, iškviečiamas Lockout įvykis (gali būti naudojamas pranešimams siųsti ir pan.)
        event(new Lockout($this));

        // Gaunamas laikas sekundėmis, po kurio bus galima bandyti vėl
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Grąžinama validacijos klaida su pranešimu iš lokalizacijos failo 'auth.throttle'
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds, // Perduodamos sekundės į lokalizacijos eilutę
                'minutes' => ceil($seconds / 60), // Apskaičiuojamos ir perduodamos minutės
            ]),
        ]);
    }

    /**
     * Generuoja unikalų "throttle" raktą šiai užklausai.
     * Raktas sudaromas iš mažosiomis raidėmis paversto el. pašto adreso ir vartotojo IP adreso.
     * Tai užtikrina, kad bandymų limitas taikomas konkrečiam el. paštui iš konkretaus IP.
     *
     * @return string Sugeneruotas "throttle" raktas.
     */
    public function throttleKey(): string
    {
        // Str::transliterate pašalina diakritinius ženklus, Str::lower paverčia mažosiomis raidėmis.
        // $this->string('email') gauna 'email' lauko reikšmę iš užklausos.
        // $this->ip() gauna užklausos IP adresą.
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
