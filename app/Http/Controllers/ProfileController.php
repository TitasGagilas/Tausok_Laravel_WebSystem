<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest; // Specializuota užklausos klasė profilio atnaujinimui
use Illuminate\Http\RedirectResponse;     // Tipas nukreipimo atsakymui
use Illuminate\Http\Request;               // Standartinis HTTP užklausos objektas
use Illuminate\Support\Facades\Auth;       // Autentifikacijos fasadas
use Illuminate\Support\Facades\Redirect;   // Nukreipimo fasadas
use Illuminate\View\View;                  // Tipas vaizdo (view) atsakymui

// ProfileController klasė paveldi iš bazinės Controller klasės
class ProfileController extends Controller
{
    /**
     * Rodo vartotojo profilio redagavimo formą.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\View\View Grąžina 'profile.edit' Blade vaizdą su vartotojo duomenimis.
     */
    public function edit(Request $request): View
    {
        // Grąžina 'profile.edit' vaizdą, perduodant jam dabartinio autentifikuoto vartotojo objektą
        return view('profile.edit', [
            'user' => $request->user(), // $request->user() gauna autentifikuoto vartotojo modelį
        ]);
    }

    /**
     * Atnaujina vartotojo profilio informaciją.
     * Naudoja ProfileUpdateRequest klasę duomenų validacijai.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request Validuota HTTP užklausa.
     * @return \Illuminate\Http\RedirectResponse Nukreipia atgal į profilio redagavimo puslapį su validacijos pranešimu.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Užpildo vartotojo modelį validuotais duomenimis iš užklausos
        $request->user()->fill($request->validated());

        // Jei vartotojo el. paštas buvo pakeistas, panaikinamas el. pašto patvirtinimo statusas
        // (reikės patvirtinti naują el. paštą, jei sistema naudos el. pašto patvirtinimą).
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Išsaugo vartotojo modelio pakeitimus duomenų bazėje
        $request->user()->save();

        // Nukreipia atgal į profilio redagavimo puslapį ('profile.edit' maršrutas)
        // ir prideda 'status' pranešimą į sesiją, kuris gali būti rodomas vaizde.
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Ištrina vartotojo paskyrą.
     *
     * @param  \Illuminate\Http\Request  $request HTTP užklausos objektas.
     * @return \Illuminate\Http\RedirectResponse Nukreipia į pradinį puslapį ('/').
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validuoja užklausą, užtikrinant, kad pateiktas teisingas dabartinis slaptažodis.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'], // Slaptažodis privalomas ir turi sutapti su dabartiniu
        ]);

        // Gaunamas autentifikuoto vartotojo modelis
        $user = $request->user();

        // Atjungiamas vartotojas nuo sistemos
        Auth::logout();

        // Ištrinamas vartotojo įrašas iš duomenų bazės
        $user->delete();

        // Panaikinama vartotojo sesija
        $request->session()->invalidate();
        // Atnaujinamas sesijos "token"
        $request->session()->regenerateToken();

        // Nukreipiama į svetainės pradinį puslapį ('/')
        return Redirect::to('/');
    }
}
