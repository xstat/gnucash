<?php

namespace App\Gnucash\Requests;

use App\Gnucash\Model\Period;

interface TransactionsRequestInterface
{
    public function getBalancesByAccountType();

    public function includeDebitsAndCredits();

    public function forAccountTypes(Array $accountTypes);
    public function exceptAccountTypes(Array $accountTypes);

    public function forPeriod(Period $period);
    public function exceptPeriod(Period $period);

    public function orderByDate();

    public function fetch();
}
