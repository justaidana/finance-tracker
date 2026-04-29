<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Budget;

class BudgetPolicy
{
    public function update(User $user, Budget $model): bool { return $user->id === $model->user_id; }
    public function delete(User $user, Budget $model): bool { return $user->id === $model->user_id; }
}
