<?php

namespace App\Gnucash\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommodityService
{
    protected $cache;

    public function __construct()
    {
        $records = $this->getCommoditiesRaw();

        $this->cache = [
            'byId'   => $records->keyBy('guid'),
            'byCode' => $records->keyBy('mnemonic')
        ];
    }

    public function getCommoditiesRaw()
    {
        return DB::connection('gnucash')->table('commodities')->get();
    }

    public function createForTransactionsCollection(Collection $transactions)
    {
        return $transactions->each(function($transaction) {
            $transaction->total = $this->create(
                $transaction->commodityId, $transaction->amount, $transaction->fraction
            );
        });
    }

    public function create($id, $amount = 0, $fraction = 1)
    {
        return new \App\Gnucash\Model\Commodity(
            $id, $this->getCodeFromId($id), $amount, $fraction
        );
    }

    public function createFromCode($code)
    {
        if ($id = $this->getIdFromCode($code)) {
            return $this->create($id);
        }
    }

    public function getIdFromCode($code)
    {
        $record = $this->cache['byCode']->get($code);
        return $record ? $record->guid : null;
    }

    public function getCodeFromId($id)
    {
        $record = $this->cache['byId']->get($id);
        return $record ? $record->mnemonic : null;
    }
}
