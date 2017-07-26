<?php

namespace App\Gnucash\Requests\Sql;

use App\Gnucash\Model\Period;
use Illuminate\Support\Facades\DB;
use App\Gnucash\Requests\TransactionsRequestInterface;

class SqlTransactionsRequest implements TransactionsRequestInterface
{
    protected $builder;
    protected $formatter;

    protected $select;
    protected $groupBy;

    public function __construct()
    {
        $this->builder = DB::connection('gnucash')
            ->table('transactions AS t')
            ->join('splits AS s', 's.tx_guid', '=', 't.guid')
            ->join('accounts AS a', 'a.guid', '=', 's.account_guid');

        $this->select = collect([
            'date'        => 't.post_date',
            'txId'        => 't.guid',
            'splitId'     => 's.guid',
            'description' => 't.description',
            'commodityId' => 'a.commodity_guid',
            'accountType' => 'a.account_type',
            'accountName' => 'a.name',
            'amount'      => 's.quantity_num',
            'fraction'    => 's.quantity_denom',
        ]);

        $this->formatter = function($collection) {
            return $collection;
        };

        $this->groupBy = [];
        $this->orderBy = [];
    }

    public function getBalancesByAccountType()
    {
        $this->select['amount'] = DB::raw(
            'SUM(s.quantity_num) AS amount'
        );

        $this->groupBy = [
            'a.account_type',
            'a.commodity_guid',
            's.quantity_denom'
        ];

        $this->formatter = function($records) {
            return $records->groupBy('accountType')->map(function($totals) {
                return app('BalanceService')->createFromCollection($totals);
            });
        };

        unset($this->select['date']);
        unset($this->select['description']);
        unset($this->select['accountName']);

        return $this;
    }

    public function getBalancesByCommodity()
    {
        $this->select['amount'] = DB::raw(
            'SUM(s.quantity_num) AS amount'
        );

        $this->groupBy = [
            'a.commodity_guid',
            's.quantity_denom'
        ];

        $this->formatter = function($records) {
            return $records->groupBy('commodityId')->map(function($items) {
                return app('BalanceService')->createFromCollection($items);
            });
        };

        unset($this->select['date']);
        unset($this->select['description']);
        unset($this->select['accountType']);
        unset($this->select['accountName']);

        return $this;
    }

    public function getTransactionsByCommodity()
    {
        $this->formatter = function($records) {
            return $records->groupBy('commodityId');
        };

        return $this;
    }

    public function includeDebitsAndCredits()
    {
        $this->select['txDebit'] = DB::raw(
            'SUM(IF(s.quantity_num < 0, s.quantity_num, 0)) AS txDebit'
        );
        $this->select['txCredits'] = DB::raw(
            'SUM(IF(s.quantity_num > 0, s.quantity_num, 0)) AS txCredit'
        );

        return $this;
    }

    public function forAccountTypes(Array $accountTypes)
    {
        $this->builder->whereIn('a.account_type', $accountTypes);

        return $this;
    }

    public function exceptAccountTypes(Array $accountTypes)
    {
        $this->builder->whereNotIn('a.account_type', $accountTypes);

        return $this;
    }

    public function forPeriod(Period $period)
    {
        if ($period->from && $period->to) {
            $this->builder->whereBetween('t.post_date', [
                $period->from->toDateTimeString(),
                $period->to->toDateTimeString()
            ]);

        } elseif ($period->from) {
            $this->builder->where(
                't.post_date', '>', $period->from->toDateTimeString()
            );

        } elseif ($period->to) {
            $this->builder->where(
                't.post_date', '<', $period->to->toDateTimeString()
            );
        }

        return $this;
    }

    public function exceptPeriod(Period $period)
    {
        return $this;
    }

    public function orderByDate()
    {
        $this->builder->orderBy('t.post_date');
        $this->builder->orderBy('t.enter_date');

        return $this;
    }

    public function fetch()
    {
        if (count($this->select)) {
            $this->builder->select($this->buildSelect());
        }

        if (count($this->groupBy)) {
            $this->builder->groupBy($this->groupBy);
        }

        return ($formatter = $this->formatter)
            ? $formatter($this->builder->get())
            : $this->builder->get();
    }

    protected function buildSelect()
    {
        $select = $this->select->map(function($column, $alias) {
            return is_string($column) ? sprintf('%s AS %s', $column, $alias) : $column;
        });

        return $select->toArray();
    }
}
