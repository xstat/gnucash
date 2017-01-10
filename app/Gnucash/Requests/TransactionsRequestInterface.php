<?php

namespace App\Gnucash\Requests;

use App\Gnucash\Model\Period;

interface TransactionsRequestInterface
{
    public function getBalancesByAccountType();

    public function includeDebitsAndCredits();

    public function onlyAccountTypes(Array $accountTypes);
    public function exceptAccountTypes(Array $accountTypes);

    public function onlyForPeriod(Period $period);
    public function exceptPeriod(Period $period);

    public function orderByDate();

    public function fetch();
}
