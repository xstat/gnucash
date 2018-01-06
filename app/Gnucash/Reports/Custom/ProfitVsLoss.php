<?php

namespace App\Gnucash\Reports\Custom;

use App\Gnucash\Gnucash;
use App\Gnucash\Reports\Report;

class ProfitVsLoss extends Report
{
    public function periods()
    {
        $periods = app('PeriodService')->getAll();
        $total = app('BalanceService')->create();

        $periods->each(function($period) use ($total) {

            $request = app('TransactionsRequestInterface');

            $balances = $request->getBalancesByCommodity()
                ->forPeriod($period)
                ->forAccountTypes([
                    Gnucash::ACCOUNT_TYPE_BANK,
                    Gnucash::ACCOUNT_TYPE_CASH,
                    Gnucash::ACCOUNT_TYPE_ASSET
                ])
                ->fetch();

            $balance = app('BalanceService')
                ->createFromBalanceCollection($balances);

            $total->sum($balance);

            $period->data->put('title', $period->from->format('Y F'));
            $period->data->put('total', $balance);
        });

        return ['periods' => $periods, 'total' => $total];
    }

    public function detail()
    {
        $period = app('PeriodService')->createFromCode(request('code'));

        if ( ! $period) {
            abort(404);
        }

        $transactions = app('TransactionsRequestInterface')
            ->getTransactionsByCommodity()
            ->forPeriod($period)
            ->forAccountTypes([
                Gnucash::ACCOUNT_TYPE_BANK,
                Gnucash::ACCOUNT_TYPE_CASH
            ])
            ->orderByDate()
            ->fetch();

        // $temp = $transactions->first()->groupBy('txId')->filter(function($items) {
        //     if ($items->count() < 2) {
        //         return false;
        //     }

        //     $balance = app('CommodityService')->create(1);
        //     $items->each(function($item) use ($balance) {
        //         $balance->sumAmount($item->amount, $item->fraction);
        //     });

        //     return ! $balance->isNull();
        // });

        // return $temp;

        $transactions->transform(function($items, $commodityId) {

            $balance = app('CommodityService')->create($commodityId);
            $credit = app('CommodityService')->create($commodityId);
            $debit = app('CommodityService')->create($commodityId);

            $items = $this->removeSettledTransactions($items, $commodityId);

            $items->each(function($item) use ($balance, $credit, $debit) {

                $total = app('CommodityService')->create(
                    $item->commodityId, $item->amount, $item->fraction
                );

                $balance->sum($total);

                if ($item->amount < 0) {
                    $debit->sum($total);
                    $item->debit = $total;
                } else {
                    $credit->sum($total);
                    $item->credit = $total;
                }

                $item->{$item->amount < 0 ? 'debit' : 'credit'} = $total;
                $item->balance = clone $balance;
            });

            $summary = clone $items->first();
            $summary->debit = $debit;
            $summary->credit = $credit;
            $summary->balance = $balance;
            $summary->description = 'Grand Total';
            $items->push($summary);

            return $items;
        });

        $txs = $transactions->mapWithKeys(function($items, $currencyId)  {
            $key = app('CommodityService')->getCodeFromId($currencyId);
            $val = array_values($items->toArray());
            return [ $key => $val ];
        });

        return ['transactions' =>  $txs];
    }

    protected function removeSettledTransactions($items, $commodityId)
    {
        $settled = $items->groupBy('txId')->reduce(function($carry, $items) use($commodityId) {
            if ($items->count() > 1 && $this->getBalance($items, $commodityId)) {
                $carry[] = $items->first()->txId;
            }
            return $carry;
        }, []);

        return $items->reject(function($item) use($settled) {
            return in_array($item->txId, $settled);
        });
    }

    protected function getBalance($items, $commodityId)
    {
        $balance = app('CommodityService')->create($commodityId);

        $items->each(function($tx) use($balance) {
            $balance->sumAmount($tx->amount, $tx->fraction);
        });

        return $balance;
    }
}
