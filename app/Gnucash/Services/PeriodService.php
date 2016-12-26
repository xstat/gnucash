<?php

namespace App\Gnucash\Services;

use DateInterval;
use Carbon\Carbon;
use App\Gnucash\Model\Period;
use App\Gnucash\Services\LedgerService;

class PeriodService
{
    public static function getAll(DateInterval $interval = null)
    {
        if (is_null($interval)) {
            $interval = new DateInterval('P1M');
        }

        $until = new Carbon('first day of next month');

        return static::create(
            LedgerService::getFirstTransactionDate(),
            $interval,
            $until->startOfDay()
        );
    }

    public static function create(Carbon $from, DateInterval $interval, Carbon $to)
    {
        $periods = array();

        $adjust = clone($from);
        $adjust->startOfMonth();

        $start = clone($from);
        $start->startOfDay();

        do {

            $end = static::apply($adjust, $interval);

            if ($start > $end) {
                // Ignore intervals that end
                // before they start.
                $end = $start;
                continue;
            }

            $periods[] = new Period(
                $start, static::substractOneSecond($end > $to ? $to : $end)
            );

        } while (($adjust = $start = $end) < $to);

        return collect($periods);
    }

    protected static function apply(Carbon $date, DateInterval $interval)
    {
        $cloned = clone($date);
        $cloned->add($interval);
        return $cloned;
    }

    protected static function substractOneSecond(Carbon $date)
    {
        $cloned = clone($date);
        $cloned->modify('-1 second');
        return $cloned;
    }
}
