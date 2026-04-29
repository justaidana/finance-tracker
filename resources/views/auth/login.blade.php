<x-guest-layout>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('app.login') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.have_account') }}</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.password') }}</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm transition">
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                {{ __('app.remember_me') }}
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:underline">{{ __('app.forgot_password') }}</a>
            @endif
        </div>
        <button type="submit"
                class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition shadow-sm">
            {{ __('app.login') }}
        </button>
    </form>

    @if (Route::has('register'))
        <p class="text-center text-sm text-gray-500 mt-6">
            {{ __('app.no_account') }}
            <a href="{{ route('register') }}" class="text-green-600 hover:underline font-medium">{{ __('app.register') }}</a>
        </p>
    @endif
</x-guest-layout>
