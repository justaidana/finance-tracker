@extends('layouts.app')
@section('title', __('app.savings_goals'))
@section('page-title', __('app.savings_goals'))

@section('content')
@php $user = auth()->user(); $sym = $user->currencySymbol(); @endphp

{{-- Add Goal button --}}
<div class="flex justify-end mb-5">
    <button onclick="document.getElementById('new-goal-form').classList.toggle('hidden')"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition shadow-sm">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
        {{ __('app.add_goal') }}
    </button>
</div>

{{-- New Goal Form --}}
<div id="new-goal-form" class="hidden bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 mb-5">
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-700 dark:text-red-400">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('savings.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @csrf
        <input type="text" name="title" placeholder="{{ __('app.savings_goals') }}" required
               class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="number" name="target_amount" placeholder="{{ __('app.target') }}" step="0.01" min="0.01" required
               class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="number" name="current_amount" placeholder="{{ __('app.current') }} ({{ __('app.optional') }})" step="0.01" min="0"
               class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="date" name="deadline"
               class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <button type="submit" class="sm:col-span-2 lg:col-span-4 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">
            {{ __('app.save') }}
        </button>
    </form>
</div>

{{-- Goals grid --}}
@forelse($goals as $goal)
@php $pct = $goal->progressPercent(); $done = $goal->isComplete(); @endphp
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 mb-4"
     x-data="{ showAdd: false, showEdit: false }">
    @if($done)
    <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl flex items-center gap-2">
        @include('components.icon', ['name' => 'check', 'class' => 'w-4 h-4 text-green-600'])
        <span class="text-sm font-medium text-green-700 dark:text-green-400">{{ __('app.goal_reached') }}</span>
    </div>
    @endif

    <div class="flex items-start justify-between mb-3">
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $goal->title }}</h3>
            @if($goal->deadline)
            <p class="text-xs text-gray-400 mt-0.5">{{ __('app.deadline') }}: {{ app()->getLocale() === 'ru' ? $goal->deadline->format('d.m.Y') : $goal->deadline->format('m/d/Y') }}</p>
            @endif
        </div>
        <div class="flex items-center gap-1">
            <button @click="showEdit=!showEdit" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                @include('components.icon', ['name' => 'pencil', 'class' => 'w-4 h-4'])
            </button>
            <button onclick="confirmDelete('{{ route('savings.destroy', $goal) }}', '{{ __('app.delete_goal') }}')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
            </button>
        </div>
    </div>

    <div class="flex justify-between text-sm mb-2">
        <span class="text-gray-600 dark:text-gray-400 tabular-nums">{{ $sym }}{{ number_format($goal->current_amount, 0, '.', ' ') }}</span>
        <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ $sym }}{{ number_format($goal->target_amount, 0, '.', ' ') }}</span>
    </div>
    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden mb-1">
        <div class="h-3 rounded-full transition-all duration-700 {{ $done ? 'bg-green-500' : 'bg-green-400' }}"
             style="width: {{ $pct }}%"></div>
    </div>
    <p class="text-right text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $pct }}%</p>

    <button @click="showAdd=!showAdd"
            class="w-full py-2 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition">
        {{ __('app.add_funds') }}
    </button>

    {{-- Add funds form --}}
    <form x-show="showAdd" x-transition method="POST" action="{{ route('savings.add', $goal) }}"
          class="mt-3 flex gap-2" style="display:none">
        @csrf
        <input type="number" name="amount" placeholder="0.00" step="0.01" min="0.01" required
               class="flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
            {{ __('app.save') }}
        </button>
    </form>

    {{-- Edit form --}}
    <form x-show="showEdit" x-transition method="POST" action="{{ route('savings.update', $goal) }}"
          class="mt-3 grid grid-cols-2 gap-3" style="display:none">
        @csrf @method('PUT')
        <input type="text" name="title" value="{{ $goal->title }}" required
               class="col-span-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="number" name="target_amount" value="{{ $goal->target_amount }}" step="0.01" min="0.01" required
               class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="number" name="current_amount" value="{{ $goal->current_amount }}" step="0.01" min="0"
               class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <input type="date" name="deadline" value="{{ $goal->deadline?->format('Y-m-d') }}"
               class="col-span-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        <button type="submit" class="col-span-2 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">
            {{ __('app.save') }}
        </button>
    </form>
</div>
@empty
<div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <p class="text-gray-400">{{ __('app.no_goals') }}</p>
    <p class="text-green-600 text-sm mt-1">{{ __('app.add_first_goal') }}</p>
</div>
@endforelse
@endsection
