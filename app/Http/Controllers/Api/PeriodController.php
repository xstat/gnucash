<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Gnucash\Services\LedgerService;
use App\Gnucash\Services\PeriodService;
use App\Gnucash\Input\InputValidator;
use App\Gnucash\Input\InputConverter;
use App\Gnucash\Input\InputException;

class PeriodController extends Controller
{
    public function summary(Request $request)
    {
        $validator = new InputValidator($request);

        $validator->requireField('from', function(InputConverter $converter) {
            return $converter->getAsCarbonDate('from', function() {
                return LedgerService::getFirstTransactionDate();
            });
        });

        $validator->requireField('interval', function(InputConverter $converter) {
            return $converter->getAsDateInterval('interval', 'P1M');
        });

        $validator->requireField('to', function(InputConverter $converter) {
            return $converter->getAsCarbonDate('to', 'first day of next month')->startOfDay();
        });

        if ($validator->ready()) {

            $periods = PeriodService::create(
                $validator->getField('from'),
                $validator->getField('interval'),
                $validator->getField('to')
            );

            return var_dump($periods->all());
        }

        return $validator->getErrors();
    }
}
