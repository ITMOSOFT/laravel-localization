<?php

namespace ItmoSoft\Localization;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;
use ItmoSoft\Localization\Models\LocalePreference;

class LocaleManager
{
    public function __construct(private readonly Application $app)
    {
    }

    public function supported(): array
    {
        return array_values(array_unique(array_filter(
            (array) config('localization.supported_locales', ['en']),
            static fn ($locale): bool => is_string($locale) && trim($locale) !== ''
        )));
    }

    public function isSupported(string $locale): bool
    {
        return in_array($locale, $this->supported(), true);
    }

    public function fallback(): string
    {
        $fallback = (string) config('localization.fallback_locale', 'en');

        if (! $this->isSupported($fallback)) {
            throw new InvalidArgumentException('The configured fallback locale must be in supported_locales.');
        }

        return $fallback;
    }

    public function setPreference(Model $subject, string $locale): void
    {
        $this->assertSupported($locale);

        LocalePreference::query()->updateOrCreate(
            [
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => (string) $subject->getKey(),
            ],
            ['locale' => $locale],
        );
    }

    public function preferenceFor(Model $subject): ?string
    {
        $locale = LocalePreference::query()
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', (string) $subject->getKey())
            ->value('locale');

        return is_string($locale) && $this->isSupported($locale) ? $locale : null;
    }

    public function forgetPreference(Model $subject): void
    {
        LocalePreference::query()
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', (string) $subject->getKey())
            ->delete();
    }

    public function resolve(?Request $request = null): string
    {
        $request ??= $this->app->make('request');
        $routeParameter = config('localization.middleware.route_parameter', 'locale');

        if (is_string($routeParameter) && $routeParameter !== '') {
            $routeLocale = $request->route($routeParameter);

            if (is_string($routeLocale) && $this->isSupported($routeLocale)) {
                return $routeLocale;
            }
        }

        if (config('localization.middleware.use_authenticated_preference', true)) {
            $user = $request->user();

            if ($user instanceof Model) {
                $preference = $this->preferenceFor($user);

                if ($preference !== null) {
                    return $preference;
                }
            }
        }

        if (config('localization.middleware.use_accept_language', true)) {
            $headerLocale = $this->matchAcceptLanguage($request->header('Accept-Language'));

            if ($headerLocale !== null) {
                return $headerLocale;
            }
        }

        return $this->fallback();
    }

    public function set(string $locale): void
    {
        $this->assertSupported($locale);
        $this->app->setLocale($locale);
    }

    private function matchAcceptLanguage(?string $header): ?string
    {
        if ($header === null || trim($header) === '') {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $candidate = trim(explode(';', $part)[0]);

            if ($candidate === '' || $candidate === '*') {
                continue;
            }

            if ($this->isSupported($candidate)) {
                return $candidate;
            }

            $base = explode('-', str_replace('_', '-', $candidate))[0];

            foreach ($this->supported() as $supported) {
                if ($supported === $base || str_starts_with($supported, $base.'-')) {
                    return $supported;
                }
            }
        }

        return null;
    }

    private function assertSupported(string $locale): void
    {
        if (! $this->isSupported($locale)) {
            throw new InvalidArgumentException("Locale [{$locale}] is not supported.");
        }
    }
}
