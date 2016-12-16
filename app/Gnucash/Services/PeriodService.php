<?php

namespace App\Gnucash\Services;

use DateInterval;
use Carbon\Carbon;
use App\Gnucash\Model\Period;

class PeriodService
{
    public static function create(Carbon $from, DateInterval $interval, Carbon $to)
    {
        $periods = array();

        $start = clone($from);
        $start->startOfMonth();

        $initial = clone($from);

        do {

            $end = static::apply($start, $interval);
            $periods[] = new Period($initial, $end > $to ? $to : static::threshold($end));

        } while (($start = $initial = $end) < $to);

        return collect($periods);
    }

    protected static function apply(Carbon $date, DateInterval $interval)
    {
        $cloned = clone($date);
        $cloned->add($interval);
        return $cloned;
    }

    protected static function threshold(Carbon $date)
    {
        $cloned = clone($date);
        $cloned->modify('-1 second');
        return $cloned;
    }
}
