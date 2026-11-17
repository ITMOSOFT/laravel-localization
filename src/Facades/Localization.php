<?php

namespace ItmoSoft\Localization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array supported()
 * @method static bool isSupported(string $locale)
 * @method static string fallback()
 * @method static void setPreference(\Illuminate\Database\Eloquent\Model $subject, string $locale)
 * @method static ?string preferenceFor(\Illuminate\Database\Eloquent\Model $subject)
 * @method static void forgetPreference(\Illuminate\Database\Eloquent\Model $subject)
 * @method static string resolve(?\Illuminate\Http\Request $request = null)
 * @method static void set(string $locale)
 */
class Localization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'localization';
    }
}
