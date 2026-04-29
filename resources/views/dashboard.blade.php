@extends('layouts.app')
@section('title', __('app.dashboard'))
@section('page-title', __('app.dashboard'))

@section('content')
@php $user = auth()->user(); $sym = $user->currencySymbol(); @endphp

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    @foreach([
        ['label' => __('app.income_month'),  'value' => $income,       'color' => 'green', 'icon' => 'trending'],
        ['label' => __('app.expense_month'), 'value' => $expenses,     'color' => 'red',   'icon' => 'receipt'],
        ['label' => __('app.balance'),       'value' => $balance,      'color' => $balance >= 0 ? 'green' : 'red', 'icon' => 'chart-bar'],
        ['label' => __('app.total_savings'), 'value' => $totalSavings, 'color' => 'blue',  'icon' => 'piggy'],
    ] as $card)
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 min-w-0">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide leading-tight">{{ $card['label'] }}</span>
            <div class="w-7 h-7 rounded-lg bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/30 flex items-center justify-center flex-shrink-0 ml-1">
                @include('components.icon', ['name' => $card['icon'], 'class' => 'w-3.5 h-3.5 text-'.$card['color'].'-600'])
            </div>
        </div>
        <div class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate tabular-nums">
            {{ $sym }}{{ number_format((float)$card['value'], 0, '.', ' ') }}
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- LEFT: Quick Add + Recent Transactions --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Quick Add Transaction --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5"
             x-data="{
                 type: 'expense',
                 loading: false,
                 async submit(e) {
                     const form = e.target;
                     this.loading = true;
                     const data = new FormData(form);
                     try {
                         const r = await fetch(form.action, {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content,
                                 'Accept': 'application/json'
                             },
                             body: data
                         });
                         const json = await r.json();
                         if (json.success) {
                             showToast(json.message, 'success');
                             form.reset();
                             form.querySelector('[name=date]').value = new Date().toISOString().slice(0,10);
                             setTimeout(() => location.reload(), 800);
                         } else {
                             const msgs = json.errors ? Object.values(json.errors).flat().join(', ') : 'Error';
                             showToast(msgs, 'error');
                         }
                     } catch(err) { showToast('Error', 'error'); }
                     this.loading = false;
                 }
             }">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white mb-4">{{ __('app.quick_add') }}</h2>
            <div class="flex gap-2 mb-4">
                <button type="button" @click="type='income'"
                        :class="type==='income' ? 'bg-green-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="flex-1 py-2 rounded-xl text-sm font-medium transition">{{ __('app.add_income') }}</button>
                <button type="button" @click="type='expense'"
                        :class="type==='expense' ? 'bg-red-500 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="flex-1 py-2 rounded-xl text-sm font-medium transition">{{ __('app.add_expense') }}</button>
            </div>

            <form @submit.prevent="submit($event)" action="{{ route('transactions.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="type" :value="type">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.amount') }}</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                               class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.date') }}</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                               class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.category') }}</label>
                    <div class="mt-1">
                        <x-select name="category_id"
                            selected=""
                            :placeholder="'— '.__('app.optional').' —'"
                            :options="collect([['value'=>'','label'=>'— '.__('app.optional').' —']])->concat(auth()->user()->categories->map(fn($c)=>['value'=>$c->id,'label'=>$c->localeName().' ('.__('app.'.$c->type).')']))->toArray()"/>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.description') }} <span class="text-gray-400">({{ __('app.optional') }})</span></label>
                    <input type="text" name="description" maxlength="500"
                           class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                </div>
                <button type="submit" :disabled="loading"
                        class="w-full py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition flex items-center justify-center gap-2 disabled:opacity-60">
                    <span x-show="loading" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                    <span>{{ __('app.save') }}</span>
                </button>
            </form>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white">{{ __('app.recent_transactions') }}</h2>
                <a href="{{ route('transactions.index') }}" class="text-sm text-green-600 hover:underline">{{ __('app.view_all') }}</a>
            </div>

            @forelse($recentTransactions as $tx)
            <div class="flex items-center gap-3 py-3 border-b border-gray-50 dark:border-gray-700 last:border-0">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: {{ $tx->category?->color ?? '#e5e7eb' }}22">
                    @include('components.icon', ['name' => $tx->category?->icon ?? 'tag', 'class' => 'w-4 h-4 text-gray-500 dark:text-gray-400'])
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $tx->description ?: ($tx->category?->localeName() ?: __('app.uncategorized')) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        {{ app()->getLocale() === 'ru' ? $tx->date->format('d.m.Y') : $tx->date->format('m/d/Y') }}
                        @if($tx->category) · {{ $tx->category->localeName() }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <span class="text-sm font-semibold {{ $tx->type === 'income' ? 'text-green-600' : 'text-red-500' }} whitespace-nowrap tabular-nums">
                        {{ $tx->type === 'income' ? '+' : '-' }}{{ $sym }}{{ number_format((float)$tx->amount, 0, '.', ' ') }}
                    </span>
                    <a href="{{ route('transactions.edit', $tx) }}" class="p-1 text-gray-400 hover:text-green-600">
                        @include('components.icon', ['name' => 'pencil', 'class' => 'w-4 h-4'])
                    </a>
                    <button onclick="confirmDelete('{{ route('transactions.destroy', $tx) }}', '{{ __('app.delete_transaction') }}')"
                            class="p-1 text-gray-400 hover:text-red-500">
                        @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <p class="text-gray-400 text-sm">{{ __('app.no_transactions') }}</p>
                <p class="text-green-600 text-sm mt-1">{{ __('app.add_first_tx') }}</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- RIGHT: Pie Chart + Budget Progress + Tip --}}
    <div class="space-y-5">

        {{-- Expense Pie Chart --}}
        @if(count($donutData) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('app.expense_chart') }}</h2>
            <canvas id="dashPieChart" height="220"></canvas>
        </div>
        @endif

        {{-- Budget Progress --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white mb-4">{{ __('app.budget_progress') }}</h2>
            @forelse($budgets as $budget)
            @php $color = $budget->status === 'over' ? 'red' : ($budget->status === 'warning' ? 'amber' : 'green'); @endphp
            <div class="mb-4 last:mb-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate max-w-[60%]">{{ $budget->category->localeName() }}</span>
                    @if($budget->status === 'over')
                        <span class="text-xs font-medium text-red-600 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">{{ __('app.over_budget') }}</span>
                    @elseif($budget->spent == 0)
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">{{ __('app.underused') }}</span>
                    @endif
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-700 bg-{{ $color }}-500"
                         style="width: {{ $budget->percent }}%"></div>
                </div>
                <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400 gap-1">
                    <span class="truncate tabular-nums">{{ $sym }}{{ number_format((float)$budget->spent, 0, '.', ' ') }} / {{ $sym }}{{ number_format((float)$budget->amount_limit, 0, '.', ' ') }}</span>
                    <span class="text-{{ $color }}-600 whitespace-nowrap tabular-nums">{{ $sym }}{{ number_format((float)$budget->remaining, 0, '.', ' ') }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-6">
                <p class="text-gray-400 text-sm">{{ __('app.no_budget') }}</p>
                <a href="{{ route('budgets.index') }}" class="text-green-600 text-sm hover:underline">{{ __('app.add_budget_cta') }}</a>
            </div>
            @endforelse
        </div>

        {{-- Financial Tip --}}
        @if($tip)
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl border border-green-100 dark:border-green-800 p-5">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs font-semibold text-green-700 dark:text-green-400 uppercase tracking-wide">Tip of the day</span>
            </div>
            <p class="text-sm text-green-900 dark:text-green-300 leading-relaxed">{{ $tip }}</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
@if(count($donutData) > 0)
<script>
window.addEventListener('fintrack:ready', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : '#f3f4f6';

    new Chart(document.getElementById('dashPieChart'), {
        type: 'doughnut',
        data: {
            labels: @json($donutLabels),
            datasets: [{
                data: @json($donutData),
                backgroundColor: @json($donutColors),
                borderWidth: 3,
                borderColor: isDark ? '#1f2937' : '#ffffff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '64%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => '  ' + ctx.label + ': ' + @json($sym) + ctx.parsed.toLocaleString()
                    }
                }
            }
        }
    });
}); // fintrack:ready
</script>
@endif
@endpush
@endsection
