<?php

namespace App\Observers;

use App\Models\Hiring;
use App\Services\PricingService;

class HiringObserver
{
    public function creating(Hiring $hiring)
    {
        $hiring->total_price = (new PricingService())->equipmentHiringPrice(
            $hiring->equipment->price,
            $hiring->start_date,
            $hiring->end_date
        );
    }
}
