<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_domain',
        'shop_gid',
        'access_token',
        'scopes',
        'is_active',
        'app_embedded_enabled',
        'app_proxy_enabled',
        'default_cookie_days',
        'default_commission_type',
        'default_commission_value',
        'commission_approval_mode',
    ];

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }
}
