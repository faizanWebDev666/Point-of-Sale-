<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class POSTransaction extends Model
{
    use HasFactory;

    protected $table = 'pos_transactions';

    protected $fillable = [
        'transaction_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'notes',
        'items',
        'loyalty_points_earned',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'items' => 'array',
        'refunded_at' => 'datetime',
    ];

    public function getTransactionNumberAttribute()
    {
        return substr($this->transaction_id, 0, 10);
    }
}
