<?php

return [
    'supported_locales' => ['en'],
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
