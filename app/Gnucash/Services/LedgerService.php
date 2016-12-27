<?php

namespace App\Gnucash\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    public static function getFirstTransactionDate()
    {
        return new Carbon(
            DB::connection('gnucash')->table('transactions')->min('post_date')
        );
    }
}
