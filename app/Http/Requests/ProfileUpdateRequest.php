<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @class ProfileUpdateRequest
 * @brief Formos užklausos klasė vartotojo profilio informacijos atnaujinimui.
 *
 * Ši klasė apibrėžia validacijos taisykles, taikomas duomenims,
 * gautiems iš profilio redagavimo formos. Ji automatiškai validuoja
 * užklausą prieš kontrolerio metodui (`ProfileController@update`) pradedant darbą.
 * Jei validacija nepavyksta, vartotojas automatiškai nukreipiamas atgal
 * su klaidų pranešimais.
 *
 * @package App\Http\Requests
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Grąžina validacijos taisykles, taikomas šiai užklausai.
     * Šios taisyklės apibrėžia, kokie laukai yra privalomi, kokio tipo jie turi būti,
     * ir kitus apribojimus (pvz., maksimalus ilgis, unikalumas).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Validacijos taisyklių masyvas.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', // Privalomas laukas
                'string',   // Turi būti eilutės tipo
                'max:255'   // Maksimalus ilgis 255 simboliai
            ],
            'email' => [
                'required',      // Privalomas laukas
                'string',        // Turi būti eilutės tipo
                'lowercase',     // Automatiškai konvertuojamas į mažąsias raides prieš validaciją
                'email',         // Turi atitikti el. pašto formatą
                'max:255',       // Maksimalus ilgis 255 simboliai

                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
