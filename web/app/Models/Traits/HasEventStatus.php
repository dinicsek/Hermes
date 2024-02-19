<?php

namespace App\Models\Traits;

use App\Models\Enums\EventStatus;
use Carbon\Carbon;

trait HasEventStatus
{
    public function calculateEventStatus(?Carbon $startDate, ?Carbon $endDate): EventStatus
    {
        if ($startDate === null || $startDate->isFuture()) {
            return EventStatus::UPCOMING;
        }

        if ($startDate->isPast() && ($endDate === null || $endDate->isFuture())) {
            return EventStatus::ONGOING;
        }

        return EventStatus::CONCLUDED;
    }
}
