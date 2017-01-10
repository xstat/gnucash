<?php

namespace App\Gnucash\Model;

use Carbon\Carbon;
use JsonSerializable;

class Period implements JsonSerializable
{
    public $data;
    public $from;
    public $to;

    public function __construct(Carbon $from = null, Carbon $to = null)
    {
        $this->data = collect();
        $this->from = $from;
        $this->to = $to;
    }

    public function JsonSerialize()
    {
        $code = base64_encode(json_encode([
            'from' => $this->from->toDateTimeString(),
            'to' => $this->to->toDateTimeString()
        ]));

        $this->data->put('code', $code);

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
