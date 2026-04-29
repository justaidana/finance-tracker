<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingsGoalRequest;
use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $goals = Auth::user()->savingsGoals()->orderByDesc('created_at')->get();
        return view('savings.index', compact('goals'));
    }

    public function store(SavingsGoalRequest $request)
    {
        Auth::user()->savingsGoals()->create($request->validated());
        return back()->with('success', __('app.goal_created'));
    }

    public function update(SavingsGoalRequest $request, SavingsGoal $saving)
    {
        $this->authorize('update', $saving);
        $saving->update($request->validated());
        return back()->with('success', __('app.goal_updated'));
    }

    public function destroy(SavingsGoal $saving)
    {
        $this->authorize('delete', $saving);
        $saving->delete();
        return back()->with('success', __('app.goal_deleted'));
    }

    public function addFunds(Request $request, SavingsGoal $saving)
    {
        $this->authorize('update', $saving);
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $amount = (float) $request->amount;

        // Пополняем цель
        $saving->increment('current_amount', $amount);

        // Находим или создаём категорию «Накопления» для этого пользователя
        $user = Auth::user();
        $savingsCategory = $user->categories()
            ->where('type', 'expense')
            ->get()
            ->first(fn($c) =>
                $c->getTranslation('name', 'ru', false) === 'Накопления' ||
                $c->getTranslation('name', 'en', false) === 'Savings'
            );

        if (!$savingsCategory) {
            $savingsCategory = $user->categories()->create([
                'name'  => ['ru' => 'Накопления', 'en' => 'Savings'],
                'type'  => 'expense',
                'color' => '#1D9E75',
                'icon'  => 'piggy',
            ]);
        }

        // Создаём expense-транзакцию чтобы баланс уменьшился
        Transaction::create([
            'user_id'     => $user->id,
            'category_id' => $savingsCategory->id,
            'type'        => 'expense',
            'amount'      => $amount,
            'description' => $saving->title,
            'date'        => Carbon::today(),
        ]);

        return back()->with('success', __('app.funds_added'));
    }
}
