<?php

namespace App\Gnucash\Model;

use App\Gnucash\Model\Commodity;
use Illuminate\Support\Collection;

class Balance
{
    protected $commodities = array();

    public function __construct(Collection $commodities = null)
    {
        if ($commodities) {

            $commodities->each(function(Commodity $commodity) {
                $this->add($commodity);
            });
        }
    }

    public function add(Commodity $commodity)
    {
        if ($stored = $this->getCommodity($commodity->getId())) {
            return $stored->sum($commodity);
        }

        $this->commodities[$commodity->getId()] = $commodity;
    }

    protected function getCommodity($commodityId)
    {
        return @ $this->commodities[$commodityId];
    }
}
