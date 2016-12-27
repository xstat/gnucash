<?php

namespace App\Gnucash\Services;

use App\Gnucash\Model\Period;
use App\Gnucash\Backends\BackendInterface;

class RepositoryService
{
    protected $backend;

    public function __construct(BackendInterface $backend)
    {
        $this->backend = $backend;
    }

    public function setOption($option, $value)
    {
        $this->backend->setOption($option, $value); return $this;
    }

    public function clearOption($option)
    {
        $this->backend->clearOption($option); return $this;
    }

    public function getTransactions()
    {
        return $this->backend->getTransactions();
    }

    public function getBalanceByAccountType(Array $accountTypes = null)
    {
        $collection = $this->backend->getTotalsByAccountType($accountTypes);

        return $collection->groupBy('accountType')->map(function($records) {
            return app('BalanceService')->createFromCollection($records);
        });
    }
}
