<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    public function update(User $user, Transaction $model): bool { return $user->id === $model->user_id; }
    public function delete(User $user, Transaction $model): bool { return $user->id === $model->user_id; }
}
