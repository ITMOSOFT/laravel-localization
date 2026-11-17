# ITMOSOFT Laravel Localization

Locale registry, persistent per-model locale preferences, and request locale resolution middleware for Laravel 12 and 13.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-localization
php artisan vendor:publish --tag=itmosoft-localization-config
php artisan migrate
```

The service provider is auto-discovered.

## Configuration

Publish `config/localization.php` to define supported locales, fallback locale, and resolution behavior.

```php
return [
    'supported_locales' => ['en', 'ur', 'ar'],
    'fallback_locale' => 'en',
    'tables' => [
        'preferences' => 'locale_preferences',
    ],
    'middleware' => [
        'route_parameter' => 'locale',
        'use_authenticated_preference' => true,
        'use_accept_language' => true,
    ],
];
```

## Locale registry

```php
use ItmoSoft\Localization\Facades\Localization;

Localization::supported(); // ['en', 'ur', 'ar']
Localization::isSupported('ur'); // true
Localization::fallback(); // 'en'
```

## Persist a subject preference

The package stores preferences against any Eloquent model using a polymorphic identity.

```php
Localization::setPreference($user, 'ur');

Localization::preferenceFor($user); // 'ur'

Localization::forgetPreference($user);
```

The configured locale must be supported; unsupported values throw `InvalidArgumentException`.

## Request middleware

Register `\ItmoSoft\Localization\Http\Middleware\SetRequestLocale::class` in your application's middleware stack or route group.

Resolution order:

1. Supported route parameter configured by `middleware.route_parameter`.
2. Authenticated user's persisted locale preference, when enabled.
3. Best supported match from the `Accept-Language` request header, when enabled.
4. Configured fallback locale.

The middleware sets Laravel's application locale for the duration of the request and calls the next middleware. It does not redirect, rewrite URLs, or persist a preference from request input.

Example route:

```php
Route::middleware(\ItmoSoft\Localization\Http\Middleware\SetRequestLocale::class)
    ->group(function () {
        Route::get('/{locale}/account', fn () => view('account'));
    });
```

Ensure the route parameter name matches `middleware.route_parameter`.

## API

| Method | Description |
| --- | --- |
| `supported(): array` | List configured supported locale codes |
| `isSupported(string $locale): bool` | Check a locale code |
| `fallback(): string` | Get configured fallback |
| `setPreference(Model $subject, string $locale): void` | Store a subject preference |
| `preferenceFor(Model $subject): ?string` | Read a subject preference |
| `forgetPreference(Model $subject): void` | Delete a subject preference |
| `resolve(?Request $request = null): string` | Resolve locale using configured precedence |
| `set(string $locale): void` | Set the Laravel application locale after validation |

## Important notes

- This package manages locale selection and preferences; Laravel's translation and pluralization systems remain responsible for translating content.
- It does not translate strings, generate translation files, translate database content, or provide RTL layout/CSS.
- `Accept-Language` matching uses locale codes and base-language matching, not a full RFC 4647 language negotiation implementation.
- Locale preferences are stored by polymorphic model type and key.
- Do not trust arbitrary locale values from user input; only configured locales are accepted.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).
