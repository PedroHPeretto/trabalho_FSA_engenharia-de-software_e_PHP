<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'cpf',
        'email',
        'role',
        'password',
        'blocked',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'blocked'  => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'made_by');
    }

    public function scopeBlocked($query)
    {
        return $query->where('blocked', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
