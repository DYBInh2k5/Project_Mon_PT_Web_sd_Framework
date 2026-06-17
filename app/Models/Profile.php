<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'avatar',
        'birthday',
        'gender',
        'phone',
    ];

    public function user(): BelongsTo
    {
        // Chiều ngược của quan hệ 1-1:
        // Mỗi profile thuộc về duy nhất 1 user.
        return $this->belongsTo(User::class);
    }
}
