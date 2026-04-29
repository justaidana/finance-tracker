<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $month = $request->filled('month')
            ? Carbon::parse($request->month)->startOfMonth()
            : Carbon::now()->startOfMonth();

        $monthStr   = $month->format('Y-m-d');
        $categories = $user->categories()->where('type', 'expense')->orderBy('id')->get();

        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->whereDate('month', $monthStr)
            ->get()
            ->keyBy('category_id');

        $categoryIds = $categories->pluck('id')->toArray();
        $spending = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereIn('category_id', $categoryIds)
            ->whereBetween('date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $totalBudgeted = $budgets->sum('amount_limit');
        $totalSpent    = $spending->sum();

        return view('budgets.index', compact(
            'categories', 'budgets', 'spending', 'month', 'totalBudgeted', 'totalSpent'
        ));
    }

    public function store(BudgetRequest $request)
    {
        $data     = $request->validated();
        $monthStr = Carbon::parse($data['month'])->startOfMonth()->format('Y-m-d');

        // Use raw upsert to avoid Carbon date cast collision
        $existing = Budget::where('user_id', Auth::id())
            ->where('category_id', $data['category_id'])
            ->whereDate('month', $monthStr)
            ->first();

        if ($existing) {
            $existing->update(['amount_limit' => $data['amount_limit']]);
        } else {
            Budget::create([
                'user_id'      => Auth::id(),
                'category_id'  => $data['category_id'],
                'amount_limit' => $data['amount_limit'],
                'month'        => $monthStr,
            ]);
        }

        return back()->with('success', __('app.budget_saved'));
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return back()->with('success', __('app.budget_deleted'));
    }
}
