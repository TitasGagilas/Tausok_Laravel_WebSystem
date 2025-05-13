<?php

namespace App\View\Components;

use Illuminate\View\Component; // Bazinė Blade komponento klasė
use Illuminate\View\View;      // Tipas vaizdo (view) objektui

/**
 * @class AppLayout
 * @brief Blade komponento klasė pagrindiniam aplikacijos išdėstymui (layout).
 *
 * Ši klasė yra atsakinga už pagrindinio aplikacijos išdėstymo Blade šablono
 * ('layouts.app') atvaizdavimą. Ji yra naudojama per <x-app-layout> žymą
 * kituose Blade šablonuose, siekiant apgaubti puslapio turinį bendra struktūra
 *
 * @package App\View\Components
 */
class AppLayout extends Component // Paveldi iš bazinės Component klasės
{
    /**
     * Gauna vaizdą / turinį, kuris reprezentuoja šį komponentą.
     *
     * Šis metodas yra pagrindinis Blade komponento klasės metodas, kuris
     * nurodo, kurį Blade šabloną naudoti šiam komponentui atvaizduoti.
     * Šiuo atveju jis grąžina 'layouts.app' vaizdą.
     *
     * @return \Illuminate\View\View Grąžina Blade vaizdo objektą.
     */
    public function render(): View // Nurodomas grąžinimo tipas (View)
    {
        return view('layouts.app');
    }
}
