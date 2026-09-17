<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Priority: session → cookie → user preference → default 'id'
        $locale = session('locale')
            ?? $request->cookie('locale')
            ?? $request->user()?->locale
            ?? config('app.locale', 'id');

        // Only allow supported locales
        $supported = ['id', 'en'];
        if (!in_array($locale, $supported)) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
