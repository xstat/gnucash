<?php

namespace App\Gnucash\Reports\Custom;

use App\Gnucash\Reports\Report;
use App\Gnucash\Services\PeriodService;
use App\Gnucash\Extractors\TotalsExtractor;

class ProfitVsLoss extends Report
{
    public function periods()
    {
        // $extractor = new TotalsExtractor();

        return PeriodService::getAll();

        $extractor->setPeriods(PeriodService::getAll());
    }
}
