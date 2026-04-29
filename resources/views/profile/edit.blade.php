@extends('layouts.app')
@section('title', __('app.profile_settings'))
@section('page-title', __('app.profile_settings'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Update Profile --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">{{ __('app.update_profile') }}</h2>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.language') }}</label>
                    <x-select name="locale" :selected="$user->locale" :options="[
                        ['value'=>'ru','label'=>'Русский'],
                        ['value'=>'en','label'=>'English'],
                    ]"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.currency') }}</label>
                    <x-select name="currency" :selected="$user->currency"
                        :options="collect(allCurrencies())->map(fn($l,$c)=>['value'=>$c,'label'=>$l])->values()->toArray()"/>
                </div>
            </div>
            <hr class="border-gray-100 dark:border-gray-700">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.new_password') }} <span class="text-gray-400 text-xs">({{ __('app.optional') }})</span></label>
                <input type="password" name="password"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.confirm_password') }}</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-green-600 text-white font-semibold text-sm hover:bg-green-700 transition">
                {{ __('app.update_profile') }}
            </button>
        </form>
    </div>

    {{-- Privacy notice --}}
    <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800 p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm text-green-800 dark:text-green-300">{{ __('app.privacy_notice') }}</p>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-100 dark:border-red-900/40 shadow-sm p-6" x-data="{ showDelete: false }">
        <h2 class="text-base font-semibold text-red-700 dark:text-red-400 mb-2">{{ __('app.delete_account') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('app.delete_warning') }}</p>
        <button @click="showDelete=!showDelete"
                class="px-5 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm font-medium border border-red-100 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/40 transition">
            {{ __('app.delete_account') }}
        </button>

        <div x-show="showDelete" x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800" style="display:none">
            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                @csrf @method('DELETE')
                <label class="block text-sm font-medium text-red-700 dark:text-red-400">{{ __('app.confirm_email_label') }}: <strong>{{ $user->email }}</strong></label>
                <input type="email" name="confirm_email" placeholder="{{ $user->email }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-red-200 dark:border-red-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                    {{ __('app.confirm') }} – {{ __('app.delete_account') }}
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
