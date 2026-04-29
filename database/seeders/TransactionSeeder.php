<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user       = User::where('email', 'demo@fintrack.app')->first();
        $categories = Category::where('user_id', $user->id)->get()->keyBy(fn($c) => $c->getTranslation('name', 'en'));

        $now  = Carbon::now();
        $prev = Carbon::now()->subMonth();

        $transactions = [
            // Current month
            ['type' => 'income',  'cat' => 'Salary',        'amount' => 450000,  'desc' => 'Monthly salary',   'date' => $now->copy()->startOfMonth()->addDays(1)],
            ['type' => 'income',  'cat' => 'Freelance',     'amount' => 85000,   'desc' => 'Web project',      'date' => $now->copy()->startOfMonth()->addDays(5)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 28000,   'desc' => 'Weekly groceries', 'date' => $now->copy()->startOfMonth()->addDays(2)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 15000,   'desc' => 'Supermarket',      'date' => $now->copy()->startOfMonth()->addDays(9)],
            ['type' => 'expense', 'cat' => 'Transport',     'amount' => 12000,   'desc' => 'Monthly pass',     'date' => $now->copy()->startOfMonth()->addDays(1)],
            ['type' => 'expense', 'cat' => 'Dining Out',    'amount' => 8500,    'desc' => 'Lunch with team',  'date' => $now->copy()->startOfMonth()->addDays(4)],
            ['type' => 'expense', 'cat' => 'Utilities',     'amount' => 18000,   'desc' => 'Electricity bill', 'date' => $now->copy()->startOfMonth()->addDays(6)],
            ['type' => 'expense', 'cat' => 'Entertainment', 'amount' => 5000,    'desc' => 'Cinema',           'date' => $now->copy()->startOfMonth()->addDays(7)],
            ['type' => 'expense', 'cat' => 'Healthcare',    'amount' => 9000,    'desc' => 'Doctor visit',     'date' => $now->copy()->startOfMonth()->addDays(10)],
            ['type' => 'expense', 'cat' => 'Clothing',      'amount' => 25000,   'desc' => 'Winter jacket',    'date' => $now->copy()->startOfMonth()->addDays(12)],

            // Previous month
            ['type' => 'income',  'cat' => 'Salary',        'amount' => 450000,  'desc' => 'Monthly salary',   'date' => $prev->copy()->startOfMonth()->addDays(1)],
            ['type' => 'income',  'cat' => 'Investments',   'amount' => 12000,   'desc' => 'Dividends',        'date' => $prev->copy()->startOfMonth()->addDays(15)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 31000,   'desc' => 'Groceries',        'date' => $prev->copy()->startOfMonth()->addDays(3)],
            ['type' => 'expense', 'cat' => 'Transport',     'amount' => 12000,   'desc' => 'Bus pass',         'date' => $prev->copy()->startOfMonth()->addDays(2)],
            ['type' => 'expense', 'cat' => 'Dining Out',    'amount' => 16000,   'desc' => 'Restaurant',       'date' => $prev->copy()->startOfMonth()->addDays(8)],
            ['type' => 'expense', 'cat' => 'Utilities',     'amount' => 17500,   'desc' => 'Utilities',        'date' => $prev->copy()->startOfMonth()->addDays(5)],
            ['type' => 'expense', 'cat' => 'Entertainment', 'amount' => 7000,    'desc' => 'Streaming',        'date' => $prev->copy()->startOfMonth()->addDays(10)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 22000,   'desc' => 'Groceries week 3', 'date' => $prev->copy()->startOfMonth()->addDays(17)],
            ['type' => 'expense', 'cat' => 'Healthcare',    'amount' => 5000,    'desc' => 'Pharmacy',         'date' => $prev->copy()->startOfMonth()->addDays(20)],
            ['type' => 'expense', 'cat' => 'Clothing',      'amount' => 18000,   'desc' => 'Shoes',            'date' => $prev->copy()->startOfMonth()->addDays(22)],

            // 2 months ago
            ['type' => 'income',  'cat' => 'Salary',        'amount' => 450000,  'desc' => 'Monthly salary',   'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(1)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 29000,   'desc' => 'Groceries',        'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(4)],
            ['type' => 'expense', 'cat' => 'Transport',     'amount' => 12000,   'desc' => 'Transport',        'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(2)],
            ['type' => 'expense', 'cat' => 'Utilities',     'amount' => 19000,   'desc' => 'Bills',            'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(7)],
            ['type' => 'expense', 'cat' => 'Dining Out',    'amount' => 11000,   'desc' => 'Dinner',           'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(14)],
            ['type' => 'expense', 'cat' => 'Entertainment', 'amount' => 6000,    'desc' => 'Events',           'date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(20)],

            // 3 months ago
            ['type' => 'income',  'cat' => 'Salary',        'amount' => 440000,  'desc' => 'Monthly salary',   'date' => $now->copy()->subMonths(3)->startOfMonth()->addDays(1)],
            ['type' => 'expense', 'cat' => 'Groceries',     'amount' => 27000,   'desc' => 'Groceries',        'date' => $now->copy()->subMonths(3)->startOfMonth()->addDays(3)],
            ['type' => 'expense', 'cat' => 'Utilities',     'amount' => 20000,   'desc' => 'Bills',            'date' => $now->copy()->subMonths(3)->startOfMonth()->addDays(6)],
            ['type' => 'expense', 'cat' => 'Transport',     'amount' => 12000,   'desc' => 'Transport',        'date' => $now->copy()->subMonths(3)->startOfMonth()->addDays(2)],
        ];

        Transaction::where('user_id', $user->id)->delete();

        foreach ($transactions as $t) {
            $cat = $categories->get($t['cat']);
            Transaction::create([
                'user_id'     => $user->id,
                'category_id' => $cat?->id,
                'type'        => $t['type'],
                'amount'      => $t['amount'],
                'description' => $t['desc'],
                'date'        => $t['date']->toDateString(),
            ]);
        }
    }
}
