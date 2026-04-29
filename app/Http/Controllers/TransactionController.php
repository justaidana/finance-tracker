<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::with('category')->where('user_id', $user->id);

        if ($request->filled('type'))       { $query->where('type', $request->type); }
        if ($request->filled('category_id')){ $query->where('category_id', $request->category_id); }
        if ($request->filled('date_from'))  { $query->whereDate('date', '>=', $request->date_from); }
        if ($request->filled('date_to'))    { $query->whereDate('date', '<=', $request->date_to); }
        if ($request->filled('search'))     { $query->where('description', 'like', '%'.$request->search.'%'); }

        $sort = $request->get('sort', 'date');
        $dir  = $request->get('dir', 'desc');
        if (in_array($sort, ['date', 'amount'])) { $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc'); }
        $query->orderByDesc('id');

        $transactions = $query->paginate(15)->withQueryString();
        $categories   = $user->categories()->orderBy('name')->get();
        $savingsGoals = SavingsGoal::where('user_id', $user->id)->orderBy('created_at')->get();

        return view('transactions.index', compact('transactions', 'categories', 'savingsGoals'));
    }

    public function store(TransactionRequest $request)
    {
        $tx = Auth::user()->transactions()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'transaction' => $tx->load('category'),
                'message'     => __('app.transaction_added'),
            ]);
        }

        return back()->with('success', __('app.transaction_added'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $categories = Auth::user()->categories()->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(TransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $transaction->update($request->validated());

        return redirect()->route('transactions.index')->with('success', __('app.transaction_updated'));
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', __('app.transaction_deleted'));
    }

    public function export()
    {
        $user         = Auth::user();
        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function () use ($transactions, $user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Type', 'Category', 'Description', 'Amount', 'Currency']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->date->toDateString(),
                    $t->type,
                    $t->category ? $t->category->localeName() : '',
                    $t->description ?? '',
                    $t->amount,
                    $user->currency,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
