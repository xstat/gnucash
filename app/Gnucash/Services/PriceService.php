<?php

namespace App\Gnucash\Services;

use App\Gnucash\Model\Price;
use Illuminate\Support\Facades\DB;

class PriceService
{
    public static function getLatestPrice($commodityId, $currencyId)
    {
        $record = DB::connection('gnucash')->table('prices')
            ->where('commodity_guid', $commodityId)
            ->where('currency_guid', $currencyId)
            ->orWhere(function($query) use( $currencyId, $commodityId) {
                $query->where('commodity_guid', $currencyId)
                      ->where('currency_guid', $commodityId);
            })
            ->orderBy('date')
            ->limit(1)
            ->first();

        if ( ! $record) {
            return null;
        }

        $price = new Price(
            $record->commodity_guid,
            $record->currency_guid,
            $record->value_num,
            $record->value_denom
        );

        if ($price->commodityId == $currencyId) {
            $price->invert();
        }

        return $price;
    }
}
