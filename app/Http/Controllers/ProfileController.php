<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'locale'   => ['required', 'in:ru,en'],
            'currency' => ['required', 'string', 'max:10'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->locale   = $data['locale'];
        $user->currency = $data['currency'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update session locale immediately
        session(['locale' => $user->locale]);

        return back()->with('success', __('app.profile_updated'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'confirm_email' => ['required', Rule::in([Auth::user()->email])],
        ], [
            'confirm_email.in' => __('app.email_mismatch'),
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();

        return redirect('/')->with('success', __('app.account_deleted'));
    }
}
