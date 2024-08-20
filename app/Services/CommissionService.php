<?php

namespace App\Services;

class CommissionService
{
    public function calculate(float $baseAmount, string $type, float $value): float
    {
        if ($type === 'fixed') {
            return round($value, 2);
        }

        return round($baseAmount * $value / 100, 2);
    }
}
