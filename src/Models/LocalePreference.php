<?php

namespace ItmoSoft\Localization\Models;

use Illuminate\Database\Eloquent\Model;

class LocalePreference extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('localization.tables.preferences', 'locale_preferences');
    }
}
