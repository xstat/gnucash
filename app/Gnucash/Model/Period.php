<?php

namespace App\Gnucash\Model;

use Carbon\Carbon;
use JsonSerializable;

class Period implements JsonSerializable
{
    public $data;
    public $from;
    public $to;

    public function __construct(Carbon $from, Carbon $to)
    {
        $this->data = collect();
        $this->from = $from;
        $this->to = $to;
    }

    public function JsonSerialize()
    {
        $this->data->put('_', [
            'from' => $this->from,
            'to' => $this->to
        ]);

        return $this->data;
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
}
