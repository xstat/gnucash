<?php

namespace App\Gnucash\Services;

use App\Gnucash\Model\Balance;
use Illuminate\Support\Collection;

class BalanceService
{
    public function create(Collection $commodities = null)
    {
        return new \App\Gnucash\Model\Balance($commodities);
    }

    // TODO: Find a better name.
    public function createFromBalanceCollection(Collection $balances)
    {
        $instance = $this->create();

        $balances->each(function(Balance $balance) use ($instance) {
            $instance->sum($balance);
        });

        return $instance;
    }

    public function createFromCollection(Collection $collection)
    {
        $commodities = $collection->groupBy('commodityId')->map(function($records, $commodityId) {

            $commodity = app('CommodityService')->create($commodityId);

            $records->each(function($item) use ($commodity) {
                $commodity->sumAmount($item->amount, $item->fraction);
            });

            return $commodity;
        });

        return $this->create($commodities);
    }
}
