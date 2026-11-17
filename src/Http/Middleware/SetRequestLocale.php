<?php

namespace ItmoSoft\Localization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use ItmoSoft\Localization\LocaleManager;
use Symfony\Component\HttpFoundation\Response;

class SetRequestLocale
{
    public function __construct(private readonly LocaleManager $locales)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->locales->set($this->locales->resolve($request));

        return $next($request);
    }
}
