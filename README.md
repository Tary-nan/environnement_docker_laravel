# Laravel v8 Multilingue dans docker et treafik

## Exemple de fichier .env à placer dans ce même répertoire

    PHP_VERSION=7.4
    PHP_NAME=laravel
    MARIADB_VERSION=10.3
    MARIADB_NAME=laraveldb
    DB_ROOT_PASSWORD=abc123
    DB_NAME=laravel
    DB_USERNAME=laravel
    DB_PASSWORD=123abc
    NET_BACKEND=backend
    NET_FRONTEND=frontend
    ADMINER_NAME=adminer
    USER_ID=1000
    GROUP_ID=1000
    NODE_VERSION=14.15.5
    DOCKER_USER=moi

## Installation

Lancer le docker
```
docker-compose up -d
```

Se connecter dans le docker
```
docker exec -it laravel /bin/bash
cd /home/moi
./run_on_dock_1st_time.sh
```

Laravel est prêt à être utilise dans ce docker

## Installation d'un site multilingue

### Définir la langue par défaut de Laravel

Dans config/app.php
```
'locale' => 'fr',
```
Dans votre dossier laravel
```
mkdir resources/lang/fr
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/json/fr.json -O resources/lang/fr.json
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/src/fr/auth.php -O resources/lang/fr/auth.php
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/src/fr/pagination.php -O resources/lang/fr/pagination.php
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/src/fr/passwords.php -O resources/lang/fr/passwords.php
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/src/fr/validation-inline.php -O resources/lang/fr/validation-inline.php
wget -c https://raw.githubusercontent.com/Laravel-Lang/lang/master/src/fr/validation.php -O resources/lang/fr/validation.php
```

#### Routes et contrôleur

Le contrôleur
```
docker exec -it laravel /bin/bash
su moi
cd /var/www/html/laravel
php artisan make:controller LocalizationController
```

Dans le fichier app/Http/Controllers/LocalizationController.php
```
<?php
namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function index($locale)
    {
        App::setlocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
```

Dans le fichier routes/web.php
```
use App\Http\Controllers\LocalizationController;

Route::get('welcome/{locale}', [LocalizationController::class, 'index'])
    ->name('welcome');
```

#### Le middleware

Sa création
```
docker exec -it laravel /bin/bash
su moi
cd /var/www/html/laravel
php artisan make:middleware Localization
```

Dans le fichier app/Http/Middleware/Localization.php
```
<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('locale')) {
            App::setlocale(session()->get('locale'));
        }
        return $next($request);
    }
}
```

Après avoir créé le middleware, il ne faut pas oublier de l'ajouter dans app/Http/Kernel.php

```
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Localization::class, // Localization Middleware
        ],
```

Utilisation dans le navigateur:
http://laravel.localhost/welcome/fr
http://laravel.localhost/welcome/en

Créer le fichier resources/lang/en/messages.php avec
```
<?php

return [
    'selected_language' => 'selected language',
];
```

Créer le fichier resources/lang/fr/messages.php avec
```
<?php

return [
    'selected_language' => 'langue sélectionnée',
];
```

Pour tester ajouter après le <body> dans le fichier resources/views/welcome.blade.php
```
<p>{{ Str::ucfirst(__('messages.selected_language')) }}: {{ str_replace('_', '-', app()->getLocale()) }}</p>
<p>{{ __("Hello!") }}</p> {{-- vient de fr.json --}}
<a href="{{ route('welcome','fr') }}">Fr</a>
<a href="{{ route('welcome','en') }}">En</a>
``
# environnement_docker_laravel
