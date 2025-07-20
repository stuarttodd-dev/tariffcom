<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
        'icon',
        'status',
        'type',
        'user_id',
    ];

    /**
     * Get the user that owns the detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
