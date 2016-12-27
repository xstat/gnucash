<?php

namespace App\Gnucash\Model;

use JsonSerializable;
use App\Gnucash\Model\Commodity;
use Illuminate\Support\Collection;

class Balance implements JsonSerializable
{
    protected $commodities;

    public function __construct(Collection $commodities = null)
    {
        $this->commodities = collect();

        if ($commodities) {
            $this->import($commodities);
        }
    }

    public function JsonSerialize()
    {
        return $this->total()->map(function($commodity) {
            return $commodity;
        })->values()->all();
    }

    public function sum(Balance $balance)
    {
        $this->import($balance->commodities);
    }

    public function import(Collection $commodities)
    {
        $commodities->each(function(Commodity $commodity) {
            $this->add($commodity);
        });
    }

    public function add(Commodity $commodity)
    {
        $this->get($commodity->getId())->sum($commodity);
    }

    public function get($commodityId)
    {
        return $this->commodities->get($commodityId) ? : $this->create($commodityId);
    }

    public function create($commodityId)
    {
        $commodity = app('CommodityService')->create($commodityId);
        $this->commodities->put($commodityId, $commodity);
        return $commodity;
    }

    public function exchange($commodityId)
    {
        $commodities = $this->commodities->map(
            function($commodity) use ($commodityId) {
                return $commodity->exchange($commodityId);
        });

        return new Balance($commodities);
    }

    public function total($commodityId = null)
    {
        if ($commodityId) {
            return $this->commodities->get($commodityId);
        }

        return $this->commodities->filter(function($commodity) {
            return ! $commodity->isNull();
        });
    }
}
