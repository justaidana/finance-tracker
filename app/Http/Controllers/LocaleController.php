<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, ['ru', 'en'])) {
            abort(404);
        }

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        session(['locale' => $locale]);

        return back();
    }
}
