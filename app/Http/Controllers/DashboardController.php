<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $now   = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        // Summary cards
        $income = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$start, $end])
            ->sum('amount');

        $expenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$start, $end])
            ->sum('amount');

        $balance      = $income - $expenses;
        $totalSavings = $user->savingsGoals()->sum('current_amount');

        // Budget progress for each budgeted category
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->whereDate('month', $start->toDateString())
            ->get()
            ->map(function ($budget) use ($user, $start, $end) {
                $spent = Transaction::where('user_id', $user->id)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('date', [$start, $end])
                    ->sum('amount');

                $budget->spent     = (float) $spent;
                $budget->remaining = max(0, $budget->amount_limit - $spent);
                $percent           = $budget->amount_limit > 0
                    ? min(100, round(($spent / $budget->amount_limit) * 100))
                    : 0;
                $budget->percent   = $percent;
                $budget->status    = $percent >= 100 ? 'over' : ($percent >= 70 ? 'warning' : 'good');

                return $budget;
            });

        // Recent transactions
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Pie chart — expense by category this month
        $pieRows = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$start, $end])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $donutLabels = $pieRows->map(fn($r) => $r->category ? $r->category->localeName() : __('app.uncategorized'))->values()->toArray();
        $donutData   = $pieRows->pluck('total')->map(fn($v) => (float)$v)->values()->toArray();
        $donutColors = $pieRows->map(fn($r) => $r->category?->color ?? '#94a3b8')->values()->toArray();

        // Tip of the day
        $tips = __('tips');
        $tip  = is_array($tips) ? $tips[date('z') % count($tips)] : '';

        return view('dashboard', compact(
            'income', 'expenses', 'balance', 'totalSavings',
            'budgets', 'recentTransactions',
            'donutLabels', 'donutData', 'donutColors',
            'tip'
        ));
    }
}
