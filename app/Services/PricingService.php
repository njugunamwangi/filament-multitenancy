<?php

namespace App\Services;

use Carbon\Carbon;

class PricingService {

    public function equipmentHiringPrice($price, $startDate, $endDate): int
    {

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay()->addDay();

        return $price * $startDate->diffInDays($endDate);

    }

}
