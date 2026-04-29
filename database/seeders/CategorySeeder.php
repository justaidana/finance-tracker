<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@fintrack.app')->first();

        $categories = [
            // Expense categories
            ['name' => ['ru' => 'Продукты',      'en' => 'Groceries'],     'type' => 'expense', 'color' => '#f59e0b', 'icon' => 'tag'],
            ['name' => ['ru' => 'Транспорт',      'en' => 'Transport'],     'type' => 'expense', 'color' => '#3b82f6', 'icon' => 'receipt'],
            ['name' => ['ru' => 'Рестораны',      'en' => 'Dining Out'],    'type' => 'expense', 'color' => '#ef4444', 'icon' => 'tag'],
            ['name' => ['ru' => 'Развлечения',    'en' => 'Entertainment'], 'type' => 'expense', 'color' => '#8b5cf6', 'icon' => 'book'],
            ['name' => ['ru' => 'Коммунальные',   'en' => 'Utilities'],     'type' => 'expense', 'color' => '#06b6d4', 'icon' => 'chart-bar'],
            ['name' => ['ru' => 'Здоровье',       'en' => 'Healthcare'],    'type' => 'expense', 'color' => '#10b981', 'icon' => 'check'],
            ['name' => ['ru' => 'Одежда',         'en' => 'Clothing'],      'type' => 'expense', 'color' => '#f97316', 'icon' => 'tag'],
            // Income categories
            ['name' => ['ru' => 'Зарплата',       'en' => 'Salary'],        'type' => 'income',  'color' => '#1D9E75', 'icon' => 'trending'],
            ['name' => ['ru' => 'Фриланс',        'en' => 'Freelance'],     'type' => 'income',  'color' => '#22c55e', 'icon' => 'trending'],
            ['name' => ['ru' => 'Инвестиции',     'en' => 'Investments'],   'type' => 'income',  'color' => '#84cc16', 'icon' => 'piggy'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'type' => $data['type'], 'color' => $data['color']],
                ['name' => $data['name'], 'icon' => $data['icon']]
            );
        }
    }
}
