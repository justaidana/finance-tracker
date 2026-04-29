@extends('layouts.app')
@section('title', __('app.analytics'))
@section('page-title', __('app.analytics'))

@section('content')
@php $user = auth()->user(); $sym = $user->currencySymbol(); @endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    {{-- Bar Chart: Income vs Expenses --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('app.income_vs_expense') }}</h2>
        <canvas id="barChart"></canvas>
    </div>

    {{-- Donut Chart: Expense by category --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('app.expense_breakdown') }}</h2>
        @if(count($donutData) > 0)
            <canvas id="donutChart"></canvas>
        @else
            <div class="flex items-center justify-center h-48 text-gray-400 text-sm">{{ __('app.no_data') }}</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Line Chart: Balance trend --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('app.balance_trend') }}</h2>
        <canvas id="lineChart"></canvas>
    </div>

    {{-- Top 5 categories table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('app.top_categories') }}</h2>
        @forelse($topCategories as $i => $row)
        <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 dark:border-gray-700 last:border-0">
            <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i+1 }}</span>
            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $row->category?->color ?? '#94a3b8' }}"></div>
            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">{{ $row->category?->localeName() ?? __('app.uncategorized') }}</span>
            <span class="text-sm font-semibold text-red-500 tabular-nums">{{ $sym }}{{ number_format((float)$row->total, 0, '.', ' ') }}</span>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-8">{{ __('app.no_data') }}</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
window.addEventListener('fintrack:ready', function() {
    const sym      = @json($sym);
    const months   = @json($months);
    const isDark   = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : '#f3f4f6';
    const tickColor = isDark ? '#9ca3af' : '#6b7280';

    const scaleStyle = {
        grid:  { color: gridColor, drawBorder: false },
        ticks: { color: tickColor, font: { size: 11 } },
        border: { display: false },
    };

    // ── Bar chart ──
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: @json(__('app.income')),
                    data: @json($incomeData),
                    backgroundColor: 'rgba(29,158,117,0.80)',
                    hoverBackgroundColor: 'rgba(29,158,117,1)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: @json(__('app.expense')),
                    data: @json($expenseData),
                    backgroundColor: 'rgba(239,68,68,0.70)',
                    hoverBackgroundColor: 'rgba(239,68,68,0.90)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => '  ' + ctx.dataset.label + ': ' + sym + ctx.parsed.y.toLocaleString() } }
            },
            scales: {
                x: { ...scaleStyle, grid: { display: false } },
                y: { ...scaleStyle, ticks: { ...scaleStyle.ticks, callback: v => sym + v.toLocaleString() } }
            }
        }
    });

    // ── Donut chart ──
    @if(count($donutData) > 0)
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: @json($donutLabels),
            datasets: [{
                data: @json($donutData),
                backgroundColor: @json($donutColors),
                borderWidth: 3,
                borderColor: isDark ? '#1f2937' : '#ffffff',
                hoverOffset: 8,
                hoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, font: { size: 11 } }
                },
                tooltip: { callbacks: { label: ctx => '  ' + ctx.label + ': ' + sym + ctx.parsed.toLocaleString() } }
            }
        }
    });
    @endif

    // ── Line chart ──
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: @json(__('app.balance')),
                data: @json($balanceData),
                borderColor: '#1D9E75',
                backgroundColor: isDark ? 'rgba(29,158,117,0.12)' : 'rgba(29,158,117,0.08)',
                borderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#1D9E75',
                pointBorderColor: isDark ? '#1f2937' : '#ffffff',
                pointBorderWidth: 2,
                tension: 0.35,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => '  ' + ctx.dataset.label + ': ' + sym + ctx.parsed.y.toLocaleString() } }
            },
            scales: {
                x: { ...scaleStyle, grid: { display: false } },
                y: { ...scaleStyle, ticks: { ...scaleStyle.ticks, callback: v => sym + v.toLocaleString() } }
            }
        }
    });
});
</script>
@endpush
@endsection
