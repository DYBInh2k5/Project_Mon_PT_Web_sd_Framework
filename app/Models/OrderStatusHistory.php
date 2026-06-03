<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'changed_by',
        'previous_status',
        'new_status',
        'note',
    ];

    public function order(): BelongsTo
    {
        // Dong history nay thuoc ve mot don hang cu the.
        return $this->belongsTo(Order::class);
    }

    public function changer(): BelongsTo
    {
        // changed_by tro ve user da doi trang thai; co the null neu he thong tu dong doi.
        return $this->belongsTo(User::class, 'changed_by');
    }
}
