<?php

namespace App\Gnucash\Services;

use DateInterval;
use Carbon\Carbon;
use App\Gnucash\Model\Period;
use App\Gnucash\Services\LedgerService;

class PeriodService
{
    public function getAll(DateInterval $interval = null)
    {
        if (is_null($interval)) {
            $interval = new DateInterval('P1M');
        }

        $until = new Carbon('first day of next month');

        return $this->createMany(
            LedgerService::getFirstTransactionDate(),
            $interval,
            $until->startOfDay()
        );
    }

    public function createFromCode($code)
    {
        $data = json_decode(base64_decode($code), true);

        return is_null($data) ? null : $this->create(
            new Carbon($data['from']),
            new Carbon($data['to'])
        );
    }

    public function create(Carbon $from = null, Carbon $to = null)
    {
        return new Period($from, $to);
    }

    public function createMany(Carbon $from, DateInterval $interval, Carbon $to)
    {
        $periods = array();

        $adjust = clone($from);
        $adjust->startOfMonth();

        $start = clone($from);
        $start->startOfDay();

        do {

            $end = $this->apply($adjust, $interval);

            if ($start > $end) {
                // Ignore intervals that end
                // before they start.
                $end = $start;
                continue;
            }

            $periods[] = $this->create(
                $start, $this->substractOneSecond($end > $to ? $to : $end)
            );

        } while (($adjust = $start = $end) < $to);

        return collect($periods);
    }

    protected function apply(Carbon $date, DateInterval $interval)
    {
        $cloned = clone($date);
        $cloned->add($interval);
        return $cloned;
    }

    protected function substractOneSecond(Carbon $date)
    {
        $cloned = clone($date);
        $cloned->modify('-1 second');
        return $cloned;
    }
}
