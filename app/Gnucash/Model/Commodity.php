<?php

namespace App\Gnucash\Model;

use JsonSerializable;
use App\Gnucash\Services\PriceService;

class Commodity implements JsonSerializable
{
    protected $id;
    protected $code;
    protected $amount;
    protected $fraction;

    public function __construct($id, $code, $amount = 0, $fraction = 1)
    {
        $this->id = (string) $id;
        $this->code = (string) $code;
        $this->amount = (int) $amount;
        $this->fraction = (int) $fraction;
    }

    public function JsonSerialize()
    {
        return [
            'id'        => $this->id,
            'code'      => $this->code,
            'data'      => [],
            'amount'    => $this->amount,
            'fraction'  => $this->fraction,
            'formatted' => $this->format(),
        ];
    }

    public function isNull()
    {
        return $this->amount == 0;
    }

    public function format()
    {
        return number_format($this->amount / $this->fraction, 2);
    }

    public function sum(Commodity $commodity)
    {
        if ($this->id != $commodity->id) {
            throw new Exception('Incompatible commodities.');
        }

        $this->sumAmount($commodity->amount, $commodity->fraction);
    }

    public function exchange($targetCommodityId)
    {
        if ($this->id == $targetCommodityId) {
            return clone $this;
        }

        $price = PriceService::getLatestPrice(
            $this->id, $targetCommodityId
        );

        return $price ? $price->exchange($this) : clone $this;
    }

    public function sumAmount($amount, $fraction = 1)
    {
        list($amount, $fraction) = $this->adjustAmount(
            $amount, $fraction, $this->fraction
        );

        list($this->amount, $this->fraction) = $this->adjustAmount(
            $this->amount, $this->fraction, $fraction
        );

        $this->amount += $amount;
    }

    protected function adjustAmount($amount, $fraction, $targetFraction)
    {
        if ($fraction < $targetFraction) {

            $ratio = $targetFraction / $fraction;
            $amount = $amount * $ratio;
            $fraction = $targetFraction;
        }

        return [(int) $amount, (int) $fraction];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getFraction()
    {
        return $this->fraction;
    }
}
