<?php

namespace App\Gnucash\Reports\Custom;

use App\Gnucash\Gnucash;
use App\Gnucash\Reports\Report;

class ProfitVsLoss extends Report
{
    protected $sections;

    protected function bootstrap()
    {
        $this->sections = [
            Gnucash::ACCOUNT_TYPE_BANK => 'Bank',
            Gnucash::ACCOUNT_TYPE_CASH => 'Cash',
            // Gnucash::ACCOUNT_TYPE_ASSET => 'Asset',
            // Gnucash::ACCOUNT_TYPE_INCOME => 'Income',
            // Gnucash::ACCOUNT_TYPE_EXPENSE => 'Expense',
            // Gnucash::ACCOUNT_TYPE_LIABILITY => 'Liability'
        ];
    }

    public function periods()
    {
        $periods = app('PeriodService')->getAll();

        $periods->each(function($period) {

            $balances = $this->loadBalances($period);

            $period->data->put('sections', $this->createSections($balances));

            $period->data->put('item', $this->createItem(
                $period->from->format('Y F'),
                app('BalanceService')->createFromBalanceCollection($balances)
            ));
        });

        return ['periods' => $periods];
    }

    public function detail()
    {
    }

    protected function loadBalances($period)
    {
        return app('RepositoryService')
            ->setOption('period', $period)
            ->getBalanceByAccountType(
                array_keys($this->sections)
            );
    }

    protected function createSections($balances)
    {
        return $balances->mapWithKeys(function($balance, $accountType) {
            return [strtolower($accountType) => $this->createItem(
                $this->sections[$accountType], $balance
            )];
        });
    }

    protected function createItem($title, $balance)
    {
        return ['title' => $title, 'total' => $balance];
    }
}
