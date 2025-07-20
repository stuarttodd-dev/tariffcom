<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prefixname',
        'firstname',
        'middlename',
        'lastname',
        'suffixname',
        'email',
        'password',
        'photo',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name for compatibility.
     */
    public function getNameAttribute(): string
    {
        $parts = array_filter([
            $this->firstname ?? '',
            $this->middlename ?? '',
            $this->lastname ?? '',
        ]);
        return trim(implode(' ', $parts));
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '' || $search === '0') {
            return $query;
        }

        return $query->whereAny([
            'firstname',
            'lastname',
            'email',
        ], 'LIKE', sprintf('%%%s%%', $search));
    }
}
