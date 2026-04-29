<x-guest-layout>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('app.register') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.no_account') }}</p>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.password') }}</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.confirm_password') }}</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm transition">
        </div>
        <button type="submit"
                class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition shadow-sm">
            {{ __('app.register') }}
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        {{ __('app.have_account') }}
        <a href="{{ route('login') }}" class="text-green-600 hover:underline font-medium">{{ __('app.login') }}</a>
    </p>
</x-guest-layout>
