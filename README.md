# Tausok - Maisto Švaistymo Mažinimo Sistema

"Tausok" yra Laravel pagrindu sukurta web aplikacija, skirta padėti maisto įstaigoms (kepyklėlėms, restoranams ir pan.) efektyviai valdyti produktų likučius, sekti jų galiojimo terminus, registruoti pardavimus, nurašymus ar aukojimus. Sistemos tikslas – mažinti maisto švaistymą, didinti veiklos tvarumą ir padėti vartotojams lengviau stebėti savo pasiekimus šioje srityje per statistiką ir pasiekimų ženklelius.

## Pagrindinės Funkcijos

* Vartotojų registracija ir autentifikacija.
* Produktų pridėjimas (dviejų žingsnių), redagavimas, sąrašo peržiūra su filtravimu ir rikiavimu.
* Detalus produktų kiekių valdymas (parduota, paaukota, išmesta, rezervuota) konkrečiai datai.
* Automatinis produkto likučio skaičiavimas remiantis transakcijomis.
* Prietaisų skydelis su pagrindiniais rodikliais ir greita produktų apžvalga.
* Statistikos puslapis su tvarumo procentu, CO2 sutaupymu, pardavimų/išmetimų diagramomis ir transakcijų istorija.
* Vartotojo profilio valdymas.
* Informacinis puslapis apie sistemą ir tvarumo ženklelius.

## Technologijos

* **Backend:** PHP 8.2+, Laravel 12
* **Frontend:** Tailwind CSS, Alpine.js, Chart.js, Vite
* **Duomenų bazė:** SQLite (pagal numatymą)
* **Paketų valdymas:** Composer (PHP), npm (Node.js)

## Diegimo ir Konfigūravimo Instrukcijos (Windows PC naudojant Terminalą ir Chocolatey)

Šios instrukcijos skirtos paleisti projektą vietinėje kūrimo aplinkoje Windows operacinėje sistemoje.

### 1. Būtinos Programos

Prieš pradedant, įsitikinkite, kad turite arba įdiekite šias programas naudodami Chocolatey (arba rankiniu būdu, jei pageidaujate). **Rekomenduojama naudoti PowerShell kaip administratoriui Chocolatey diegimui ir komandoms.**

* **Chocolatey (Paketų Valdymo Įrankis):**
  Jei neturite, įdiekite sekdami instrukcijas [chocolatey.org/install](https://chocolatey.org/install). Atidarykite PowerShell kaip administratorius ir vykdykite nurodytą komandą. Po įdiegimo perkraukite terminalą.

* **PHP (>= 8.2):**
  Atidarykite terminalą (PowerShell kaip administratorius arba CMD) ir vykdykite:
    ```powershell
    choco install php --version=8.2 --params "/InstallDir:C:\tools\php82"
    ```
  (Galite pasirinkti naujesnę 8.x versiją, pvz., 8.3, ir kitą diegimo vietą. Po diegimo gali reikėti perkrauti terminalą.)
  Patikrinkite: `php --version`
  **Svarbu dėl SSL:** Po PHP įdiegimo, jums reikės sukonfigūruoti `php.ini` failą, kad veiktų Composer SSL ryšiai:
    1.  Atsisiųskite naujausią `cacert.pem` iš [https://curl.se/docs/caextract.html](https://curl.se/docs/caextract.html).
    2.  Sukurkite aplanką, pvz., `C:\tools\php82\extras\ssl\` ir įdėkite ten `cacert.pem`.
    3.  Raskite ir redaguokite `php.ini` failą (paprastai `C:\tools\php82\php.ini`).
    4.  Suraskite arba pridėkite šias eilutes (pakeiskite kelią, jei reikia, ir nuimkite `;` jei eilutės užkomentuotos):
        ```ini
        curl.cainfo = "C:/tools/php82/extras/ssl/cacert.pem"
        openssl.cafile = "C:/tools/php82/extras/ssl/cacert.pem"
        ```
    5.  Išsaugokite `php.ini`. Jei naudojate web serverį (ne `php artisan serve`), jį reikės perkrauti.

* **Composer (PHP Paketų Valdymo Įrankis):**
    ```powershell
    choco install composer
    ```
  (Paprastai įdiegus PHP per Chocolatey, Composer nereikia diegti atskirai, jei pasirinkote atitinkamus parametrus, bet ši komanda užtikrins, kad jis yra.)
  Patikrinkite: `composer --version`

* **Node.js (su npm):**
    ```powershell
    choco install nodejs-lts
    ```
  Patikrinkite: `node --version` ir `npm --version`

* **Git (Versijų Kontrolės Sistema - Rekomenduojama):**
    ```powershell
    choco install git
    ```

### 2. Projekto Paruošimas

1.  **Klonuokite arba Nukopijuokite Projektą:**
    Jei projektas yra Git repozitorijoje:
    ```bash
    git clone https://github.com/TitasGagilas/Tausok_Laravel_WebSystem
    cd Tausok_Laravel_WebSystem
    ```
    Arba tiesiog nukopijuokite visą projekto aplanką į norimą vietą savo kompiuteryje (pvz., `C:\Projektai\taupyk-maista`) ir pereikite į jį terminale:
    ```bash
    cd C:\Projektai\taupyk-maista
    ```

2.  **Įdiekite PHP Priklausomybes:**
    ```bash
    composer install
    ```
    *(Jei susiduriate su SSL klaidomis, įsitikinkite, kad `php.ini` sukonfigūruotas teisingai, kaip aprašyta PHP diegimo žingsnyje. Gali prireikti terminalą paleisti kaip administratoriui, jei `composer install` bando rašyti į apsaugotas direktorijas arba jei Windows teisių problemos trukdo Git operacijoms, kai paketai bandomi atsisiųsti iš "source")*

3.  **Įdiekite Node.js Priklausomybes:**
    ```bash
    npm install
    ```

4.  **Sukonfigūruokite Aplinkos Failą:**
    * Nukopijuokite pavyzdinį `.env` failą:
        ```bash
        copy .env.example .env
        ```
    * Sugeneruokite unikalią aplikacijos raktą:
        ```bash
        php artisan key:generate
        ```
    * Atidarykite `.env` failą su tekstų redaktoriumi ir peržiūrėkite/pakeiskite šiuos nustatymus, jei reikia:
        ```dotenv
        APP_NAME=Tausok
        APP_ENV=local
        APP_DEBUG=true
        APP_URL=http://localhost:8000

        DB_CONNECTION=sqlite
        DB_DATABASE=database/database.sqlite # Kelias skaičiuojamas nuo projekto šaknies

        # Kiti nustatymai pagal poreikį (pvz., Mailtrap el. pašto testavimui)
        MAIL_MAILER=log # Kūrimo metu saugu naudoti log, kad laiškai nebūtų siunčiami
        ```

5.  **Vykdykite Duomenų Bazės Migracijas:**
    (Tai sukurs `database/database.sqlite` failą, jei jo nėra, ir visas reikiamas lenteles)
    ```bash
    php artisan migrate
    ```

6.  **Sukurkite Saugyklos Nuorodą:**
    (Tai leis pasiekti failus iš `storage/app/public` per `public/storage`)
    ```bash
    php artisan storage:link
    ```
    *(Windows sistemoje šiai komandai gali reikėti administratoriaus teisių terminalui)*

7.  **Sukompiliuokite Front-End Resursus:**
    ```bash
    npm run build
    ```

### 3. Projekto Paleidimas

1.  **Paleiskite Laravel Kūrimo Serverį:**
    ```bash
    php artisan serve
    ```
    Terminalas parodys adresą, kuriuo veikia serveris (paprastai `http://127.0.0.1:8000`).

2.  **Atidarykite Aplikaciją Naršyklėje:**
    Įveskite terminale parodytą adresą (pvz., `http://127.0.0.1:8000`) į savo interneto naršyklę. Jūsų "Tausok" aplikacija turėtų pasileisti.

## Papildoma Informacija

* **Teisių Problemos ("Permission denied"):**
    Jei vykdant `composer install` (ypač kai bandoma atsisiųsti iš "source" per Git) ar `php artisan storage:link` susiduriate su "Permission denied" klaidomis, įsitikinkite, kad jūsų terminalas turi pakankamai teisių. Kartais padeda **terminalo paleidimas administratoriaus teisėmis** (dešiniu pelės mygtuku ant PowerShell/CMD ikonos -> Run as administrator).

* **PATH Kintamųjų Konfigūravimas (PHP ir Composer):**
    Jei įdiegėte PHP ir/ar Composer rankiniu būdu (ne per Chocolatey ar Laragon, kurie dažniausiai tai padaro automatiškai) ir komandos `php` ar `composer` neatpažįstamos terminale, jums reikės pridėti jų diegimo katalogus prie Windows PATH aplinkos kintamojo:
    1.  **Suraskite diegimo kelius:**
        * PHP: katalogas, kuriame yra `php.exe` (pvz., `C:\php` arba `C:\tools\php82`).
        * Composer: katalogas, kuriame yra `composer.phar` arba Composer `bin` aplankas (pvz., `C:\ProgramData\ComposerSetup\bin` arba `C:\Users\JūsųVartotojas\AppData\Roaming\Composer\vendor\bin`).
    2.  **Atidarykite Aplinkos Kintamuosius:**
        * Windows paieškoje įveskite "environment variables".
        * Pasirinkite "Edit the system environment variables".
    3.  **Redaguokite PATH:**
        * "System Properties" lange, "Advanced" skiltyje, spauskite "Environment Variables...".
        * "System variables" (arba "User variables", jei norite pakeisti tik savo vartotojui) sąraše raskite kintamąjį pavadinimu `Path`. Pasirinkite jį ir spauskite "Edit...".
    4.  **Pridėkite Kelius:**
        * Atsidariusiame lange spauskite "New" ir pridėkite pilną kelią iki PHP katalogo.
        * Dar kartą spauskite "New" ir pridėkite pilną kelią iki Composer katalogo.
    5.  **Išsaugokite Pakeitimus:** Spauskite "OK" visuose atidarytuose languose.
    6.  **Perkraukite Terminalą:** **Būtinai uždarykite ir iš naujo atidarykite visus terminalo langus**, kad pakeitimai įsigaliotų.
    7.  **Patikrinkite:** Naujame terminale įveskite `php --version` ir `composer --version`. Komandos turėtų būti atpažįstamos.

* **Būtinų PHP Plėtinių Įjungimas `php.ini`:**
    Laravel ir daugelis PHP projektų reikalauja tam tikrų PHP plėtinių. Įsitikinkite, kad jūsų `php.ini` faile (kurį radote diegdami PHP, pvz., `C:\tools\php82\php.ini`, arba galite rasti kelią įvedę `php --ini` terminale) yra įjungti šie plėtiniai (t.y., eilutė neprasideda kabliataškiu `;`):
    ```ini
    extension=fileinfo
    extension=pdo_sqlite
    extension=sqlite3
    ```
    Po pakeitimų `php.ini` faile, **išsaugokite jį** ir **perkraukite web serverį** (jei naudojate Apache/Nginx per Laragon klasikiniu būdu) arba **sustabdykite ir vėl paleiskite `php artisan serve`** (Laragon atveju, galite tiesiog "Stop" ir "Start All" servisus Laragon programoje).

Sėkmės paleidžiant projektą!
