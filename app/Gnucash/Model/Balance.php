<?php

namespace App\Gnucash\Model;

use App\Gnucash\Model\Commodity;
use Illuminate\Support\Collection;

class Balance
{
    protected $commodities;

    public function __construct(Collection $commodities = null)
    {
        $this->commodities = collect();

        if (is_null($commodities)) {
            return;
        }

        $commodities->each(function(Commodity $commodity) {
            $this->add($commodity);
        });
    }

    public function add(Commodity $commodity)
    {
        if ( ! $this->commodities->has($commodity->guid)) {

            $this->commodities->put(
                $commodity->guid, new Commodity($commodity->guid)
            );
        }

        $this->commodities->get($commodity->guid)->sum($commodity);
    }

    public function exchange($commodityId)
    {
        return new Balance($this->exchangeCommodities($commodityId));
    }

    public function getTotal($commodityId = null)
    {
        if ( ! $commodityId) {
            return $this->commodities;
        }

        $commodities = $this->exchangeCommodities($commodityId);

        return $commodities->get(
            $commodityId, new Commodity($commodityId)
        );
    }

    protected function exchangeCommodities($commodityId)
    {
        return $this->commodities->map(function($commodity) use ($commodityId) {
            return $commodity->exchange($commodityId);
        });
    }

    public function getCommodities()
    {
        return $this->commodities;
    }
}
