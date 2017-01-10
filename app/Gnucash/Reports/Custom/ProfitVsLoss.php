<?php

namespace App\Gnucash\Reports\Custom;

use App\Gnucash\Gnucash;
use App\Gnucash\Reports\Report;

class ProfitVsLoss extends Report
{
    public function periods()
    {
        $periods = app('PeriodService')->getAll();

        $periods->each(function($period) {

            $request = app('TransactionsRequestInterface');

            $balances = $request->getBalancesByCommodity()
                ->onlyForPeriod($period)
                ->onlyAccountTypes([
                    Gnucash::ACCOUNT_TYPE_BANK,
                    Gnucash::ACCOUNT_TYPE_CASH
                ])
                ->fetch();

            $balance = app('BalanceService')
                ->createFromBalanceCollection($balances);

            $period->data->put('title', $period->from->format('Y F'));
            $period->data->put('total', $balance);
        });

        return ['periods' => $periods];
    }

    public function detail()
    {
        $code = request('code');
        $period = app('PeriodService')->createFromCode($code);

        if ( ! $code || ! $period) {
            abort(404);
        }

        $balancePeriod = app('PeriodService')->create(null, $period->from);

        $balances = app('TransactionsRequestInterface')
            ->getBalancesByCommodity()
            ->onlyForPeriod($balancePeriod)
            ->onlyAccountTypes([
                Gnucash::ACCOUNT_TYPE_BANK,
                Gnucash::ACCOUNT_TYPE_CASH
            ])
            ->fetch();

        $transactions = app('TransactionsRequestInterface')
            ->getTransactionsByCommodity()
            ->onlyForPeriod($period)
            ->onlyAccountTypes([
                Gnucash::ACCOUNT_TYPE_BANK,
                Gnucash::ACCOUNT_TYPE_CASH
            ])
            ->orderByDate()
            ->fetch();

        $transactions->each(function($items, $commodityId) use ($balances) {

            if ( ! $balance = $balances->get($commodityId)) {
                $balance = app('BalanceService')->create();
            }

            $item = clone $items->first();
            $item->description = 'Cummulative Balance';
            $item->total = $balance->get($commodityId);
            $item->amount = $item->total->getAmount();
            $item->fraction = $item->total->getFraction();

            $items->prepend($item);
        });

        return ['transactions' => $transactions];
    }
}
