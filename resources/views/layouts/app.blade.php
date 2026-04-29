<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FinTrack') }} – @yield('title', __('app.dashboard'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Prevent x-cloak flash and apply dark mode class BEFORE paint --}}
    <style>[x-cloak]{display:none!important}</style>
    <script>
        (function(){
            var t=localStorage.getItem('theme');
            if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-200">

{{-- Toast container --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none" aria-live="polite"></div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('{{ addslashes(session('success')) }}','success'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('{{ addslashes(session('error')) }}','error'));</script>
@endif

<div class="flex min-h-screen" x-data="{ open: false }">

    {{-- Mobile backdrop --}}
    <div x-cloak x-show="open" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"
         @click="open=false"></div>

    {{-- ── SIDEBAR ── --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col
                  bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 shadow-xl
                  -translate-x-full transition-transform duration-200 ease-in-out
                  lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none lg:z-auto lg:flex-shrink-0"
           :class="open ? 'translate-x-0' : ''">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <span class="font-bold text-gray-900 dark:text-white text-lg tracking-tight">FinTrack</span>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
            @php
            $links = [
                ['route' => 'dashboard',         'icon' => 'home',      'label' => __('app.dashboard')],
                ['route' => 'transactions.index', 'icon' => 'receipt',   'label' => __('app.transactions')],
                ['route' => 'budgets.index',      'icon' => 'chart-bar', 'label' => __('app.budgets')],
                ['route' => 'savings.index',      'icon' => 'piggy',     'label' => __('app.savings')],
                ['route' => 'analytics.index',    'icon' => 'trending',  'label' => __('app.analytics')],
                ['route' => 'advice',             'icon' => 'book',      'label' => __('app.advice')],
                ['route' => 'categories.index',   'icon' => 'tag',       'label' => __('app.categories')],
            ];
            @endphp

            @foreach($links as $link)
            @php
            $active = request()->routeIs($link['route'])
                   || (str_contains($link['route'], '.') && request()->routeIs(explode('.', $link['route'])[0].'*'));
            @endphp
            <a href="{{ route($link['route']) }}"
               @click="open=false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors group
                      {{ $active
                           ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                           : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-gray-100' }}">
                @include('components.icon', ['name' => $link['icon'], 'class' => 'w-5 h-5 flex-shrink-0 '.($active?'text-green-600 dark:text-green-400':'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300')])
                {{ $link['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- Trust badge --}}
        <div class="px-3 pb-4 flex-shrink-0">
            <div class="flex items-start gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
                <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs text-green-700 dark:text-green-400 leading-snug">{{ __('app.trust_badge') }}</p>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 h-14 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-10">
            {{-- Hamburger --}}
            <button @click="open=true"
                    class="lg:hidden p-2 -ml-1 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-gray-800 dark:text-white text-sm sm:text-base truncate">@yield('page-title', __('app.dashboard'))</span>

            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                {{-- Dark mode toggle --}}
                <button id="theme-toggle"
                        onclick="toggleTheme()"
                        class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                        title="Toggle dark mode">
                    {{-- Sun icon (shown in dark mode) --}}
                    <svg id="theme-sun" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    {{-- Moon icon (shown in light mode) --}}
                    <svg id="theme-moon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- Language switcher --}}
                <div class="hidden sm:flex items-center text-sm bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5">
                    <a href="{{ route('locale.switch', 'ru') }}"
                       class="px-2.5 py-1 rounded-md font-medium transition {{ app()->getLocale()==='ru' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">RU</a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-md font-medium transition {{ app()->getLocale()==='en' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">EN</a>
                </div>

                {{-- User dropdown --}}
                <div class="relative" x-data="{ menu: false }">
                    <button @click="menu=!menu"
                            class="flex items-center gap-2 py-1 px-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="w-7 h-7 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-cloak x-show="menu" x-transition
                         @click.outside="menu=false"
                         class="absolute right-0 mt-1.5 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('app.profile') }}</a>
                        {{-- Mobile locale switcher --}}
                        <div class="sm:hidden px-4 py-2 flex gap-2 text-sm border-t border-gray-50 dark:border-gray-700 mt-1">
                            <a href="{{ route('locale.switch', 'ru') }}" class="px-2 py-0.5 rounded {{ app()->getLocale()==='ru' ? 'bg-green-100 text-green-700 font-medium' : 'text-gray-500 dark:text-gray-400' }}">RU</a>
                            <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-0.5 rounded {{ app()->getLocale()==='en' ? 'bg-green-100 text-green-700 font-medium' : 'text-gray-500 dark:text-gray-400' }}">EN</a>
                        </div>
                        <hr class="my-1 border-gray-100 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">{{ __('app.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Confirmation modal --}}
<div id="confirm-modal"
     x-data="{ show: false, url: '', method: 'DELETE', message: '' }"
     x-cloak
     x-show="show"
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50" @click="show=false"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 z-10">
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="message"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('app.confirm_delete') }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button @click="show=false"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                {{ __('app.cancel') }}
            </button>
            <form :action="url" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="_method" :value="method">
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                    {{ __('app.delete') }}
                </button>
            </form>
        </div>
    </div>
</div>

@stack('scripts')

<script>
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        document.getElementById('theme-sun').classList.add('hidden');
        document.getElementById('theme-moon').classList.remove('hidden');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        document.getElementById('theme-moon').classList.add('hidden');
        document.getElementById('theme-sun').classList.remove('hidden');
    }
}

// Init icon state
(function() {
    if (document.documentElement.classList.contains('dark')) {
        document.getElementById('theme-moon').classList.add('hidden');
        document.getElementById('theme-sun').classList.remove('hidden');
    }
})();

function confirmDelete(url, message, method = 'DELETE') {
    const el = document.getElementById('confirm-modal');
    if (el) {
        const data = Alpine.$data(el);
        data.url     = url;
        data.message = message;
        data.method  = method;
        data.show    = true;
    }
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const colors = type === 'success'
        ? 'bg-white dark:bg-gray-800 border-green-200 dark:border-green-700 text-green-800 dark:text-green-300'
        : 'bg-white dark:bg-gray-800 border-red-200 dark:border-red-700 text-red-800 dark:text-red-300';
    const dot = type === 'success' ? 'bg-green-500' : 'bg-red-500';

    const el = document.createElement('div');
    el.className = `flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium
                    pointer-events-auto opacity-0 translate-y-1 transition-all duration-300 ${colors}`;
    el.innerHTML = `<span class="w-2 h-2 rounded-full flex-shrink-0 ${dot}"></span><span>${message}</span>`;
    container.appendChild(el);

    requestAnimationFrame(() => {
        el.classList.remove('opacity-0', 'translate-y-1');
    });

    setTimeout(() => {
        el.classList.add('opacity-0');
        setTimeout(() => el.remove(), 300);
    }, 3000);
}
</script>
</body>
</html>
