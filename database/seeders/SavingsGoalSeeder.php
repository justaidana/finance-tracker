<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SavingsGoalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@fintrack.app')->first();

        SavingsGoal::where('user_id', $user->id)->delete();

        SavingsGoal::create([
            'user_id'        => $user->id,
            'title'          => 'Отпуск в Европе',
            'target_amount'  => 500000,
            'current_amount' => 300000, // 60%
            'deadline'       => Carbon::now()->addMonths(6)->toDateString(),
        ]);

        SavingsGoal::create([
            'user_id'        => $user->id,
            'title'          => 'Резервный фонд',
            'target_amount'  => 300000,
            'current_amount' => 300000, // 100% — completed!
            'deadline'       => null,
        ]);

        SavingsGoal::create([
            'user_id'        => $user->id,
            'title'          => 'Новый ноутбук',
            'target_amount'  => 250000,
            'current_amount' => 80000,
            'deadline'       => Carbon::now()->addMonths(4)->toDateString(),
        ]);
    }
}
