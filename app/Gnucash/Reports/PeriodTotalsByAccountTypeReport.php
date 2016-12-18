<?php

namespace App\Gnucash\Reports;

use App\Gnucash\Model\Balance;
use App\Gnucash\Model\Commodity;
use Illuminate\Support\Facades\DB;
use App\Gnucash\Services\LedgerService;

class PeriodTotalsByAccountTypeReport
{
    protected $query;

    public function __construct()
    {
        $this->query = DB::table('transactions AS t');

        $this->includeSplits();
        $this->includeAccounts();
        $this->groupByAccountType();

        $this->query->select(
            'a.account_type',
            'a.commodity_guid AS commodity',
            's.quantity_denom AS fraction',
            DB::raw('SUM(s.quantity_num) AS amount')
        );
    }

    protected function includeSplits()
    {
        $this->query->rightJoin('splits AS s', 't.guid', '=', 's.tx_guid');
    }

    protected function includeAccounts()
    {
        $this->query->join('accounts AS a', 'a.guid', '=', 's.account_guid');
    }

    protected function groupByAccountType()
    {
        $this->query->groupBy('a.account_type', 'a.commodity_guid', 's.quantity_denom');
    }

    public function run()
    {
        $rs = $this->query->get();

        $result = $rs->groupBy('account_type')->mapWithKeys(function($items, $accType) {
            $commodities = LedgerService::getCommoditiesFromCollection($items);
            return [$accType => new Balance($commodities)];
        });

        return collect($result);
    }
}
