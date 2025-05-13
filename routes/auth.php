<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController; // Atsakingas už prisijungimo sesijos valdymą (login, logout)
use App\Http\Controllers\Auth\ConfirmablePasswordController; // Slaptažodžio patvirtinimui prieš jautrias operacijas
use App\Http\Controllers\Auth\EmailVerificationNotificationController; // El. pašto patvirtinimo pranešimo siuntimui
use App\Http\Controllers\Auth\EmailVerificationPromptController;    // El. pašto patvirtinimo raginimo rodymui
use App\Http\Controllers\Auth\NewPasswordController;                // Naujo slaptažodžio nustatymui (po atstatymo)
use App\Http\Controllers\Auth\PasswordController;                   // Vartotojo slaptažodžio keitimui (kai jau prisijungęs)
use App\Http\Controllers\Auth\PasswordResetLinkController;          // Slaptažodžio atstatymo nuorodos siuntimui
use App\Http\Controllers\Auth\RegisteredUserController;             // Naujų vartotojų registracijai
use App\Http\Controllers\Auth\VerifyEmailController;                // El. pašto adreso patvirtinimui per nuorodą
use Illuminate\Support\Facades\Route; // Laravel maršrutų fasadas

// Maršrutų grupė, skirta svečiams (neautentifikuotiems vartotojams)
// 'guest' middleware užtikrina, kad šie maršrutai bus prieinami tik tada,
// kai vartotojas NĖRA prisijungęs. Jei prisijungęs vartotojas bandys pasiekti
// šiuos maršrutus, jis bus nukreiptas į '/home' (arba kitą numatytąjį puslapį,
// apibrėžtą RouteServiceProvider).
Route::middleware('guest')->group(function () {
    // Registracijos formos rodymo maršrutas
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register'); // Maršruto pavadinimas, naudojamas nuorodoms generuoti

    // Registracijos formos duomenų apdorojimo maršrutas (POST metodas)
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Prisijungimo formos rodymo maršrutas
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Prisijungimo formos duomenų apdorojimo maršrutas (POST metodas)
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // "Pamiršau slaptažodį" formos rodymo maršrutas
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request'); // Maršruto pavadinimas slaptažodžio užklausai

    // "Pamiršau slaptažodį" formos duomenų apdorojimo (el. laiško siuntimo) maršrutas (POST metodas)
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email'); // Maršruto pavadinimas slaptažodžio el. laiškui

    // Slaptažodžio atstatymo formos rodymo maršrutas (su unikaliu žetonu URL'e)
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset'); // Maršruto pavadinimas slaptažodžio atstatymui

    // Slaptažodžio atstatymo formos duomenų apdorojimo maršrutas (POST metodas)
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store'); // Maršruto pavadinimas naujo slaptažodžio išsaugojimui
});

// Maršrutų grupė, skirta autentifikuotiems vartotojams
// 'auth' middleware užtikrina, kad šie maršrutai bus prieinami tik tada,
// kai vartotojas YRA prisijungęs.
Route::middleware('auth')->group(function () {
    // El. pašto patvirtinimo raginimo rodymo maršrutas
    // Naudojamas, jei vartotojas turi patvirtinti savo el. paštą, bet dar to nepadarė.
    // Pastaba: čia naudojamas "invokable" controller, todėl metodo pavadinimas nenurodomas.
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice'); // Maršruto pavadinimas patvirtinimo pranešimui

    // El. pašto patvirtinimo maršrutas (kai vartotojas paspaudžia nuorodą iš el. laiško)
    // '{id}' ir '{hash}' yra parametrai, perduodami iš patvirtinimo nuorodos.
    // 'signed' middleware užtikrina, kad nuoroda yra pasirašyta ir nebuvo modifikuota.
    // 'throttle:6,1' riboja bandymų skaičių (6 bandymai per 1 minutę) šiam maršrutui.
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify'); // Maršruto pavadinimas el. pašto patvirtinimui

    // Pakartotinio el. pašto patvirtinimo pranešimo siuntimo maršrutas (POST metodas)
    // 'throttle:6,1' taip pat riboja bandymų skaičių.
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send'); // Maršruto pavadinimas patvirtinimo laiško siuntimui

    // Slaptažodžio patvirtinimo formos rodymo maršrutas (prieš atliekant jautrias operacijas)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Slaptažodžio patvirtinimo formos duomenų apdorojimo maršrutas (POST metodas)
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Vartotojo slaptažodžio keitimo maršrutas (kai vartotojas jau prisijungęs ir nori pakeisti slaptažodį)
    // Naudoja PUT metodą.
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Vartotojo atsijungimo maršrutas (POST metodas)
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
