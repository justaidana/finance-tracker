@extends('layouts.app')
@section('title', __('app.budgets'))
@section('page-title', __('app.budgets'))

@section('content')
@php $user = auth()->user(); $sym = $user->currencySymbol(); @endphp

{{-- Month navigator --}}
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('budgets.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
       class="p-2 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h2 class="text-base font-semibold text-gray-900 dark:text-white min-w-[160px] text-center">
        {{ ucfirst($month->translatedFormat('F Y')) }}
    </h2>
    <a href="{{ route('budgets.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
       class="p-2 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

{{-- Summary --}}
@if($budgets->count())
<div class="grid grid-cols-2 gap-3 mb-5">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('app.total_budgeted') }}</p>
        <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $sym }}{{ number_format($totalBudgeted, 0, '.', ' ') }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('app.total_spent') }}</p>
        <p class="text-lg font-bold tabular-nums {{ $totalSpent > $totalBudgeted ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ $sym }}{{ number_format($totalSpent, 0, '.', ' ') }}</p>
    </div>
</div>
@endif

@if($budgets->where('percent', '>=', 100)->count())
<div class="mb-5 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    {{ __('app.over_budget_alert') }}
</div>
@endif

{{-- Category budget cards — all categories listed, scroll naturally --}}
@if($categories->isEmpty())
<div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
    <p class="text-gray-400 text-sm">{{ __('app.no_categories') }}</p>
    <a href="{{ route('categories.index') }}" class="text-green-600 text-sm hover:underline">{{ __('app.add_category') }} →</a>
</div>
@else
<div class="space-y-3">
    @foreach($categories as $cat)
    @php
        $budget  = $budgets->get($cat->id);
        $spent   = (float)($spending->get($cat->id, 0));
        $limit   = $budget ? (float)$budget->amount_limit : 0;
        $percent = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;
        $color   = $percent >= 100 ? 'red' : ($percent >= 70 ? 'amber' : 'green');
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4"
         x-data="{ editing: false }">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: {{ $cat->color }}22">
                @include('components.icon', ['name' => $cat->icon ?? 'tag', 'class' => 'w-4 h-4'])
            </div>
            <span class="flex-1 font-medium text-gray-800 dark:text-gray-100 text-sm">{{ $cat->localeName() }}</span>
            <div class="flex items-center gap-2">
                @if($budget)
                    @if($percent >= 100)
                        <span class="text-xs font-medium text-red-600 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">{{ __('app.over_budget') }}</span>
                    @elseif($spent == 0)
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-full">{{ __('app.underused') }}</span>
                    @endif
                @endif
                <button @click="editing=!editing"
                        class="text-xs font-medium text-green-600 hover:text-green-700 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 px-2.5 py-1 rounded-lg transition">
                    {{ $budget ? __('app.edit') : __('app.set_budget') }}
                </button>
            </div>
        </div>

        @if($budget)
        <div class="mb-2">
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full transition-all duration-700
                    {{ $color === 'red' ? 'bg-red-500' : ($color === 'amber' ? 'bg-amber-500' : 'bg-green-500') }}"
                     style="width: {{ $percent }}%"></div>
            </div>
            <div class="flex justify-between mt-1.5 text-xs">
                <span class="text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ $sym }}{{ number_format($spent, 0, '.', ' ') }} / {{ $sym }}{{ number_format($limit, 0, '.', ' ') }}
                </span>
                <span class="tabular-nums font-medium
                    {{ $color === 'red' ? 'text-red-600' : ($color === 'amber' ? 'text-amber-600' : 'text-green-600') }}">
                    {{ __('app.remaining') }}: {{ $sym }}{{ number_format(max(0, $limit - $spent), 0, '.', ' ') }}
                </span>
            </div>
        </div>
        @else
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ __('app.no_budget') }}</p>
        @endif

        {{-- Set/edit form --}}
        <form x-show="editing" x-transition method="POST" action="{{ route('budgets.store') }}"
              class="mt-2 flex gap-2" style="display:none">
            @csrf
            <input type="hidden" name="category_id" value="{{ $cat->id }}">
            <input type="hidden" name="month" value="{{ $month->format('Y-m-d') }}">
            <input type="number" name="amount_limit" step="0.01" min="0.01"
                   value="{{ $budget?->amount_limit }}" placeholder="0"
                   required
                   class="flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
                {{ __('app.save') }}
            </button>
            @if($budget)
            <button type="button"
                    onclick="confirmDelete('{{ route('budgets.destroy', $budget) }}', '{{ __('app.budget_deleted') }}')"
                    class="px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 text-sm hover:bg-red-100 transition">
                @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
            </button>
            @endif
        </form>
    </div>
    @endforeach
</div>
@endif
@endsection
