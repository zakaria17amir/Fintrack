<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'month' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSpentAttribute(): int
    {
        return Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $this->month->month)
            ->whereYear('transaction_date', $this->month->year)
            ->sum('amount');
    }

    public function getPercentageAttribute(): int
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return min(round(($this->spent / $this->amount) * 100), 100);
    }
}
