<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'ru';

        if (auth()->check()) {
            $locale = auth()->user()->locale ?? 'ru';
        } elseif ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        App::setLocale(in_array($locale, ['ru', 'en']) ? $locale : 'ru');

        return $next($request);
    }
}
