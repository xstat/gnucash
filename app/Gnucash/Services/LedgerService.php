<?php

namespace App\Gnucash\Services;

use Carbon\Carbon;
use App\Gnucash\Model\Commodity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    public static function getFirstTransactionDate()
    {
        return new Carbon(
            DB::connection('gnucash')->table('transactions')->min('post_date')
        );
    }

    public static function getCommoditiesFromCollection(Collection $collection)
    {
        $commodities = $collection->groupBy('commodity')->map(function($items, $commodityId) {

            $commodity = new Commodity($commodityId);

            $items->each(function($item) use ($commodity) {
                $commodity->addAmount($item->amount, $item->fraction);
            });

            return $commodity;
        });

        return collect($commodities);
    }
}
