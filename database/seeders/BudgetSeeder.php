<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $user       = User::where('email', 'demo@fintrack.app')->first();
        $categories = Category::where('user_id', $user->id)->get()->keyBy(fn($c) => $c->getTranslation('name', 'en'));
        $month      = Carbon::now()->startOfMonth()->toDateString();

        $limits = [
            'Groceries'     => 80000,
            'Transport'     => 15000,
            'Dining Out'    => 30000,
            'Entertainment' => 20000,
            'Utilities'     => 25000,
        ];

        Budget::where('user_id', $user->id)->where('month', $month)->delete();

        foreach ($limits as $catName => $limit) {
            $cat = $categories->get($catName);
            if ($cat) {
                Budget::create([
                    'user_id'      => $user->id,
                    'category_id'  => $cat->id,
                    'amount_limit' => $limit,
                    'month'        => $month,
                ]);
            }
        }
    }
}
