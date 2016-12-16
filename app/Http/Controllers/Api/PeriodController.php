<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Gnucash\Time\PeriodFactory;
use App\Gnucash\Helpers\InputHelper;
use App\Http\Controllers\Controller;

class PeriodController extends Controller
{
    public function summary(Request $request)
    {
        $periods = $this->loadPeriods($request);

        var_dump($periods);
    }

    protected function loadPeriods(Request $request)
    {
        $input = new InputHelper($request);

        return PeriodFactory::create(
            $input->getDate('from', 'now -1 month'),
            $input->getDateInterval('interval', 'P1M'),
            $input->getDate('to', 'now')
        );
    }
}
