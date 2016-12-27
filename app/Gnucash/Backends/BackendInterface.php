<?php

namespace App\Gnucash\Backends;

interface BackendInterface
{
    public function getTotalsByAccountType(Array $accountTypes = null);
    public function getTransactions();
}
