<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'book_id',
        'user_id',
        'loaned_at',
        'due_date',
        'returned_at',
        'has_fine',
        'fine_paid',
    ];

    protected function casts(): array
    {
        return [
            'loaned_at'   => 'datetime',
            'due_date'    => 'datetime',
            'returned_at' => 'datetime',
            'has_fine'    => 'boolean',
            'fine_paid'   => 'boolean',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')->where('due_date', '<', now());
    }
}
