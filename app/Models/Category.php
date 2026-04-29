<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['user_id', 'name', 'type', 'color', 'icon'];
    public array $translatable = ['name'];

    protected $casts = ['name' => 'array'];

    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
    public function budgets(): HasMany      { return $this->hasMany(Budget::class); }

    /** Localized name shortcut */
    public function localeName(): string
    {
        return $this->getTranslation('name', app()->getLocale(), false)
            ?: $this->getTranslation('name', 'ru', false)
            ?: '';
    }
}
