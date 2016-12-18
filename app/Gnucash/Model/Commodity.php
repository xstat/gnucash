<?php

namespace App\Gnucash\Model;

use App\Gnucash\Services\PriceService;

class Commodity
{
    public $guid;
    protected $amount;
    protected $fraction;

    public function __construct($guid, $amount = 0, $fraction = 1)
    {
        $this->guid = $guid;
        $this->amount = (int) $amount;
        $this->fraction = (int) $fraction;
    }

    public function addAmount($amount, $fraction = 1)
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

    public function sum(Commodity $commodity)
    {
        if ($this->guid != $commodity->guid) {
            throw new Exception('Incompatible commodities.');
        }

        $this->addAmount(
            $commodity->getAmount(),
            $commodity->getFraction()
        );
    }

    public function exchange($commodityId)
    {
        if ($this->guid == $commodityId) {
            return clone $this;
        }

        $price = PriceService::getLatestPrice(
            $this->guid, $commodityId
        );

        return $price ? $price->exchange($this) : clone $this;
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
