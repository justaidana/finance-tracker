<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsGoal extends Model
{
    protected $fillable = ['user_id', 'title', 'target_amount', 'current_amount', 'deadline'];
    protected $casts    = ['deadline' => 'date', 'target_amount' => 'decimal:2', 'current_amount' => 'decimal:2'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function progressPercent(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function isComplete(): bool { return $this->current_amount >= $this->target_amount; }
}
