<?php

namespace App\Gnucash\Time;

use DateInterval;
use Carbon\Carbon;

class PeriodFactory
{
    public static function create(Carbon $from, DateInterval $interval, Carbon $to)
    {
        $periods = array();

        $start = clone($from);

        do {

            $end = static::apply($start, $interval);
            $periods[] = new Period($start, $end > $to ? $to : static::threshold($end));

        } while (($start = $end) < $to);

        return $periods;
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
