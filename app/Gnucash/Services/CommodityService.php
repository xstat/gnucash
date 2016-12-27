<?php

namespace App\Gnucash\Services;

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

    public function create($id, $code = null)
    {
        $code = $code ? : $this->getCodeFromId($id);
        return new \App\Gnucash\Model\Commodity($id, $code);
    }

    public function createFromCode($code)
    {
        if ($id = $this->getIdFromCode($code)) {
            return $this->create($id, $code);
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
