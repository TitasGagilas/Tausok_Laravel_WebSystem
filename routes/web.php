<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route; // Pagrindinis Laravel maršrutų fasadas

use App\Http\Controllers\ProductQuantityController;
use App\Http\Controllers\DashboardController;

/*
| Čia galite registruoti "web" maršrutus savo aplikacijai.
| Šie maršrutai yra įkeliami per RouteServiceProvider ir jiems visiems
| automatiškai priskiriama 'web' middleware grupė (kuri apima, pvz., sesijos valdymą, CSRF apsaugą).
*/

// Pagrindinis maršrutas ('/')
// Nukreipia vartotoją į prisijungimo puslapį ('/login')
Route::get('/', function () {
    return redirect('/login');
});

// Prietaisų skydelio (Dashboard) maršrutas
// Kai vartotojas pasiekia '/dashboard' URL, kviečiamas DashboardController index metodas.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified']) // Taikomi keli middleware
    ->name('dashboard');

// Maršrutų grupė, kuriai taikomas 'auth' middleware (prieinama tik autentifikuotiems vartotojams)
Route::middleware('auth')->group(function () {
    // Profilio redagavimo formos rodymo maršrutas
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Profilio duomenų atnaujinimo maršrutas (PATCH metodas)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Vartotojo paskyros trynimo maršrutas (DELETE metodas)
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Informacinio puslapio ('/info') maršrutas
    // Route::view() yra trumpinys paprastam vaizdui (view) grąžinti be jokios kontrolerio logikos.
    Route::view('/info', 'info.index')->name('info.index');

    // Produktų kūrimo žingsnių maršrutai
    // Pirmas žingsnis: formos rodymas (GET) ir duomenų išsaugojimas (POST)
    Route::get('/product/create/step-1', [ProductController::class, 'createStep1'])->name('product.create.step1');
    Route::post('/product/create/step-1', [ProductController::class, 'storeStep1'])->name('product.store.step1');
    // Antras žingsnis: formos rodymas (GET) ir galutinis produkto išsaugojimas (POST)
    Route::get('/product/create/step-2', [ProductController::class, 'createStep2'])->name('product.create.step2');
    Route::post('/product/create/step-2', [ProductController::class, 'storeStep2'])->name('product.store.step2');

    // --- Produktų Kiekių Valdymo Maršrutai ---
    // Kiekių valdymo lentelės puslapio rodymas (GET)
    Route::get('/products/quantity', [ProductQuantityController::class, 'index'])
        ->name('products.quantity.index');

    // Kiekių koregavimo formos duomenų apdorojimas (POST)
    Route::post('/products/quantity', [ProductQuantityController::class, 'store'])
        ->name('products.quantity.store'); // Maršruto pavadinimas reikalingas formai
    // --- Produktų Kiekių Valdymo Maršrutų Pabaiga ---


});

// Produktų sąrašo puslapio maršrutas
// Individualus 'auth' middleware (galėtų būti aukščiau esančioje grupėje)
Route::get('/products', [ProductController::class, 'index'])
    ->middleware(['auth']) // Užtikrina, kad tik autentifikuoti vartotojai matys produktų sąrašą
    ->name('products.index');

// Tvarumo (Statistikos) puslapio maršrutas
Route::get('/sustainability', [\App\Http\Controllers\SustainabilityController::class, 'index'])->name('sustainability.index');

// Produkto redagavimo formos rodymo maršrutas (su produkto ID kaip parametru)
// Individualus 'auth' middleware
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->middleware(['auth'])
    ->name('products.edit'); // {product} yra Route Model Binding parametras

// Produkto duomenų atnaujinimo maršrutas (PUT metodas)
// Individualus 'auth' middleware
Route::put('/products/{product}', [ProductController::class, 'update'])
    ->middleware(['auth'])
    ->name('products.update');

// Produkto trynimo maršrutas (DELETE metodas)
// Individualus 'auth' middleware
Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('products.destroy');

// Įkeliami standartiniai Laravel autentifikacijos maršrutai (login, register, password reset ir t.t.)
// Šie maršrutai apibrėžti 'auth.php' faile.
require __DIR__.'/auth.php';
