<?php

namespace App\Gnucash\Model;

use App\Gnucash\Model\Commodity;

class Price
{
    public $commodityId;
    public $currencyId;
    public $value;
    public $denom;

    public function __construct($commodityId, $currencyId, $value, $denom)
    {
        $this->commodityId = $commodityId;
        $this->currencyId = $currencyId;
        $this->value = $value;
        $this->denom = $denom;
    }

    public function exchange(Commodity $commodity)
    {
        $exchange = new Commodity($this->currencyId);

        $amount = $commodity->getAmount() * $this->value / $this->denom;
        $exchange->addAmount((int) $amount, $commodity->getFraction());

        return $exchange;
    }

    public function invert()
    {
        $temp = $this->commodityId;
        $this->commodityId = $this->currencyId;
        $this->currencyId = $temp;

        $temp = $this->value;
        $this->value = $this->denom;
        $this->denom = $temp;
    }
}
