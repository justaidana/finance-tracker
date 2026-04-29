<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SavingsGoal;

class SavingsGoalPolicy
{
    public function update(User $user, SavingsGoal $model): bool { return $user->id === $model->user_id; }
    public function delete(User $user, SavingsGoal $model): bool { return $user->id === $model->user_id; }
}
