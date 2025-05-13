<?php

namespace App\View\Components;

use Illuminate\View\Component; // Bazinė Blade komponento klasė
use Illuminate\View\View;      // Tipas vaizdo (view) objektui

/**
 * @class GuestLayout
 * @brief Blade komponento klasė svečio (neautentifikuoto vartotojo) puslapių išdėstymui.
 *
 * Ši klasė yra atsakinga už svečio puslapių išdėstymo Blade šablono
 * ('layouts.guest') atvaizdavimą. Ji naudojama per <x-guest-layout> žymą
 * tokiuose puslapiuose kaip prisijungimas, registracija ir pan.,
 * siekiant suteikti jiems paprastesnę ir nuoseklią struktūrą,
 * dažnai be navigacijos juostos, skirtos autentifikuotiems vartotojams.
 *
 * @package App\View\Components
 */
class GuestLayout extends Component // Paveldi iš bazinės Component klasės
{
    /**
     * Gauna vaizdą / turinį, kuris reprezentuoja šį komponentą.
     *
     * Šis metodas nurodo, kurį Blade šabloną naudoti šiam komponentui atvaizduoti.
     * Šiuo atveju jis grąžina 'layouts.guest' vaizdą.
     *
     * @return \Illuminate\View\View Grąžina Blade vaizdo objektą.
     */
    public function render(): View // Nurodomas grąžinimo tipas (View)
    {
        return view('layouts.guest');
    }
}
