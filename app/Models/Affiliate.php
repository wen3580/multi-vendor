<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'customer_id', 'email', 'first_name', 'last_name', 'phone', 'status',
        'referral_code', 'referral_slug', 'default_discount_code', 'commission_type', 'commission_value',
        'payout_method', 'payout_account', 'notes',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
