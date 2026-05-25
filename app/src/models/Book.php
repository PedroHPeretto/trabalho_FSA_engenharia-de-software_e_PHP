<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'media',
        'stock',
        'digital_link',
        'cover_image',
        'pdf',
        'reserved',
        'reserve_expiration',
        'reserved_to',
        'fine',
    ];

    protected function casts(): array
    {
        return [
            'reserved'           => 'boolean',
            'fine'               => 'boolean',
            'reserve_expiration' => 'datetime',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_to');
    }

    public function scopePhysical($query)
    {
        return $query->where('media', 'physical');
    }

    public function scopeDigital($query)
    {
        return $query->where('media', 'digital');
    }

    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('media', 'physical')->where('stock', '>', 0);
        })->orWhere(function ($q) {
            $q->where('media', 'digital')->where('reserved', false);
        });
    }
}
