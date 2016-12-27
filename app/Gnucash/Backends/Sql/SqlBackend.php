<?php

namespace App\Gnucash\Backends\Sql;

use Illuminate\Support\Facades\DB;
use App\Gnucash\Backends\BackendAbstract;

class SqlBackend extends BackendAbstract
{
    public function getTotalsByAccountType(Array $accountTypes = null)
    {
        $builder = DB::connection('gnucash')
            ->table('transactions AS t')
            ->select(
                'a.account_type AS accountType',
                'a.commodity_guid AS commodityId',
                's.quantity_denom AS fraction',
                DB::raw('SUM(s.quantity_num) AS amount')
            )
            ->groupBy(['accountType', 'commodityId', 'fraction']);

        if ($accountTypes) {
            $builder->whereIn('a.account_type', $accountTypes);
        }

        $this->joinSplits($builder);
        $this->joinAccounts($builder);
        $this->filterPeriod($builder);

        return $builder->get();
    }

    public function getTransactions()
    {
        $builder = DB::connection('gnucash')
            ->table('transactions AS t')
            ->select(
                't.post_date AS date',
                't.description AS description',
                'a.name AS account',
                'a.account_type AS accountType',
                'a.commodity_guid AS commodityId',
                's.quantity_num AS amount',
                's.quantity_denom AS fraction'
            );

        $this->joinSplits($builder);
        $this->joinAccounts($builder);
        $this->filterPeriod($builder);

        return $builder->get();
    }

    protected function joinSplits($builder)
    {
        $builder->rightJoin('splits AS s', 't.guid', '=', 's.tx_guid');
    }

    protected function joinAccounts($builder)
    {
        $builder->join('accounts AS a', 'a.guid', '=', 's.account_guid');
    }

    protected function filterPeriod($builder)
    {
        if ($period = $this->getOption('period')) {
            $builder->whereBetween('t.post_date', [
                $period->from->toDateTimeString(),
                $period->to->toDateTimeString()
            ]);
        }
    }
}
