<?php

namespace App\Gnucash\Time;

use Carbon\Carbon;
use JsonSerializable;

class Period implements JsonSerializable
{
    protected $from;
    protected $to;

    public function __construct(Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function isFuture()
    {
        return $this->from->isFuture();
    }

    public function isPast()
    {
        return $this->to->isPast();
    }

    public function isToday()
    {
        return Carbon::now()->between($this->from, $this->to);
    }

    public function JsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to
        ];
    }
}
