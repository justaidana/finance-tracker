@extends('layouts.app')
@section('title', __('app.transactions'))
@section('page-title', __('app.transactions'))

@section('content')
@php $user = auth()->user(); $sym = $user->currencySymbol(); @endphp

{{-- Tab switcher --}}
<div class="flex gap-1 mb-5 p-1 bg-gray-100 dark:bg-gray-800 rounded-2xl w-fit border border-gray-200 dark:border-gray-700">
    <a href="{{ route('transactions.index', request()->except('tab')) }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition
              {{ request('tab') !== 'savings' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        {{ __('app.transactions') }}
    </a>
    <a href="{{ route('transactions.index', array_merge(request()->query(), ['tab' => 'savings'])) }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition
              {{ request('tab') === 'savings' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        {{ __('app.savings') }}
    </a>
</div>

@if(request('tab') === 'savings')
{{-- ── SAVINGS TAB ── --}}
<div class="space-y-4">
    @forelse($savingsGoals as $goal)
    @php $pct = $goal->progressPercent(); $done = $goal->isComplete(); @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4"
         x-data="{ showAdd: false }">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                @include('components.icon', ['name' => 'piggy', 'class' => 'w-4 h-4 text-green-600'])
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $goal->title }}</p>
                @if($goal->deadline)
                <p class="text-xs text-gray-400">{{ __('app.deadline') }}: {{ app()->getLocale() === 'ru' ? $goal->deadline->format('d.m.Y') : $goal->deadline->format('m/d/Y') }}</p>
                @endif
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-semibold tabular-nums {{ $done ? 'text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
                    {{ $sym }}{{ number_format($goal->current_amount, 0, '.', ' ') }}
                    <span class="text-xs font-normal text-gray-400">/ {{ $sym }}{{ number_format($goal->target_amount, 0, '.', ' ') }}</span>
                </p>
                @if($done)
                <span class="text-xs text-green-600 font-medium">✓ {{ __('app.goal_reached') }}</span>
                @else
                <span class="text-xs text-gray-400">{{ $pct }}%</span>
                @endif
            </div>
        </div>
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden mb-3">
            <div class="h-2 rounded-full transition-all duration-700 {{ $done ? 'bg-green-500' : 'bg-green-400' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        <button @click="showAdd=!showAdd"
                class="w-full py-2 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition">
            {{ __('app.add_funds') }}
        </button>
        <form x-show="showAdd" x-transition method="POST" action="{{ route('savings.add', $goal) }}"
              class="mt-3 flex gap-2" style="display:none">
            @csrf
            <input type="number" name="amount" placeholder="0.00" step="0.01" min="0.01" required
                   class="flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
            <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
                {{ __('app.save') }}
            </button>
        </form>
    </div>
    @empty
    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
        <p class="text-gray-400 text-sm">{{ __('app.no_goals') }}</p>
        <a href="{{ route('savings.index') }}" class="text-green-600 text-sm hover:underline">{{ __('app.add_first_goal') }} →</a>
    </div>
    @endforelse
</div>

@else
{{-- ── TRANSACTIONS TAB ── --}}

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-5">
    <form method="GET" class="space-y-3">
        <input type="hidden" name="tab" value="">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <x-select name="type"
                :selected="request('type', '')"
                :options="[
                    ['value' => '',        'label' => __('app.all_types')],
                    ['value' => 'income',  'label' => __('app.income')],
                    ['value' => 'expense', 'label' => __('app.expense')],
                ]"/>
            <x-select name="category_id"
                :selected="request('category_id', '')"
                :options="collect([['value'=>'','label'=>__('app.all_categories')]])->concat($categories->map(fn($c)=>['value'=>$c->id,'label'=>$c->localeName()]))->toArray()"/>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('app.search') }}"
                   class="col-span-2 sm:col-span-1 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">{{ __('app.filter') }}</button>
            <a href="{{ route('transactions.index') }}" class="px-4 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reset</a>
            <a href="{{ route('transactions.export') }}"
               class="ml-auto flex items-center gap-1.5 px-4 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                @include('components.icon', ['name' => 'download', 'class' => 'w-4 h-4'])
                {{ __('app.export_csv') }}
            </a>
        </div>
    </form>
</div>

{{-- List --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    @forelse($transactions as $tx)
    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition last:border-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background: {{ $tx->category?->color ?? '#e5e7eb' }}22">
            @include('components.icon', ['name' => $tx->category?->icon ?? 'tag', 'class' => 'w-4 h-4 text-gray-500 dark:text-gray-400'])
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                {{ $tx->description ?: ($tx->category?->localeName() ?: __('app.uncategorized')) }}
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                {{ app()->getLocale()==='ru' ? $tx->date->format('d.m.Y') : $tx->date->format('m/d/Y') }}
                @if($tx->category) · {{ $tx->category->localeName() }} @endif
            </p>
        </div>
        <span class="text-sm font-semibold {{ $tx->type==='income' ? 'text-green-600' : 'text-red-500' }} whitespace-nowrap tabular-nums flex-shrink-0">
            {{ $tx->type==='income' ? '+' : '-' }}{{ $sym }}{{ number_format((float)$tx->amount, 0, '.', ' ') }}
        </span>
        <div class="flex items-center gap-0.5 flex-shrink-0">
            <a href="{{ route('transactions.edit', $tx) }}"
               class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                @include('components.icon', ['name' => 'pencil', 'class' => 'w-4 h-4'])
            </a>
            <button onclick="confirmDelete('{{ route('transactions.destroy', $tx) }}','{{ __('app.delete_transaction') }}')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
            </button>
        </div>
    </div>
    @empty
    <div class="text-center py-16">
        <p class="text-gray-400 text-sm">{{ __('app.no_transactions') }}</p>
        <p class="text-green-600 text-sm mt-1">{{ __('app.add_first_tx') }}</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endif

@endsection
