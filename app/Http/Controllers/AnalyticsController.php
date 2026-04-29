<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Last 6 months labels + income/expense data
        $months = [];
        $incomeData  = [];
        $expenseData = [];
        $balanceData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->startOfMonth();
            $months[] = $month->translatedFormat(app()->getLocale() === 'ru' ? 'M Y' : 'M Y');

            $inc = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereBetween('date', [$month, $month->copy()->endOfMonth()])
                ->sum('amount');
            $exp = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('date', [$month, $month->copy()->endOfMonth()])
                ->sum('amount');

            $incomeData[]  = (float)$inc;
            $expenseData[] = (float)$exp;
            $balanceData[] = round((float)$inc - (float)$exp, 2);
        }

        // Donut – expense breakdown by category this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $categoryExpenses = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('category_id')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $donutLabels = $categoryExpenses->map(fn($t) => $t->category ? $t->category->localeName() : __('app.uncategorized'))->values()->toArray();
        $donutData   = $categoryExpenses->pluck('total')->map(fn($v) => (float)$v)->values()->toArray();
        $donutColors = $categoryExpenses->map(fn($t) => $t->category?->color ?? '#1D9E75')->values()->toArray();

        // Top 5 categories
        $topCategories = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('category')
            ->get();

        return view('analytics.index', compact(
            'months', 'incomeData', 'expenseData', 'balanceData',
            'donutLabels', 'donutData', 'donutColors', 'topCategories'
        ));
    }
}
