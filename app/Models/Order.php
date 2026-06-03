<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'processing',
        'shipped',
        'completed',
        'cancelled',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid',
        'paid',
    ];

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'notes',
        'status',
        'payment_status',
        'payment_method',
        'transaction_code',
        'paid_at',
        'total_amount',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        // Mot don hang co nhieu lan doi trang thai.
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $nested) use ($search) {
            $nested->where('order_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        // Local scope giup controller goi ->status($status) thay vi viet if/query lap lai.
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopePlacedFrom(Builder $query, mixed $date): Builder
    {
        return $date ? $query->whereDate('placed_at', '>=', $date) : $query;
    }

    public function scopePlacedUntil(Builder $query, mixed $date): Builder
    {
        return $date ? $query->whereDate('placed_at', '<=', $date) : $query;
    }
}
