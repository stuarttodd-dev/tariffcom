<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $full_name
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $middle_initial
 * @property string $prefixname
 * @property string $photo
 * @property string $gender
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

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
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->firstname, $this->getMiddleInitialAttribute(), $this->lastname]);
        return implode(' ', $parts);
    }

    /**
     * Get the user's middle initial.
     */
    public function getMiddleInitialAttribute(): ?string
    {
        return $this->middlename ? strtoupper(substr($this->middlename, 0, 1)) . '.' : null;
    }

    /**
     * Get the user's gender based on prefixname.
     */
    public function getGenderAttribute(): string
    {
        return match ($this->prefixname) {
            'Mr' => 'Male',
            'Mrs', 'Ms' => 'Female',
            default => 'Unknown'
        };
    }

    /**
     * @return HasMany<Detail, User>
     */
    public function details(): HasMany
    {
        return $this->hasMany(Detail::class);
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('type', 'admin');
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeRegularUsers(Builder $query): Builder
    {
        return $query->where('type', 'user');
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeWithPrefix(Builder $query, $prefix): Builder
    {
        return $query->where('prefixname', $prefix);
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * @param  Builder<User> $query
     * @return Builder<User>
     */
    public function scopeWithPhotos(Builder $query): Builder
    {
        return $query->whereNotNull('photo')->where('photo', '!=', '');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null || $search === '' || $search === '0') {
            return $query;
        }

        return $query->whereAny([
            'firstname',
            'lastname',
            'email',
        ], 'LIKE', sprintf('%%%s%%', $search));
    }
}
