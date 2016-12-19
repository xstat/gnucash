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

        if ($commodities) {
            $this->importCommodities($commodities);
        }
    }

    protected function importCommodities(Collection $commodities)
    {
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
        $commodities = $this->commodities->map(
            function($commodity) use ($commodityId) {
                return $commodity->exchange($commodityId);
        });

        return new Balance($commodities);
    }

    public function getTotal($commodityId = null)
    {
        if ( ! $commodityId) {
            return $this->commodities;
        }

        return $this->commodities->get(
            $commodityId, new Commodity($commodityId)
        );
    }

    public function getCommodities()
    {
        return $this->commodities;
    }
}
