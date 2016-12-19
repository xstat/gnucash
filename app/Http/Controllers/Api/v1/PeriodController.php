<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Gnucash\Services\LedgerService;
use App\Gnucash\Services\PeriodService;
use App\Gnucash\Input\InputValidator;
use App\Gnucash\Input\InputConverter;
use App\Gnucash\Input\InputException;

use App\Gnucash\Reports\PeriodTotalsByAccountTypeReport;

class PeriodController extends Controller
{
    public function summary(Request $request)
    {
        $c = [
            'adf' => '02fb06d6816c80d85c4cf67ee229fe07',
            'ars' => '1c9c24cb4b2a4cc51d15499065e7dc0c',
            'usd' => 'c7e767c471c47b3cac6b4494df7dcbce',
            'uyu' => 'ddd1ccbd2334b15a51cd5ac4f58be260'
        ];

        $balance = new \App\Gnucash\Model\Balance();

        $balance->add(new \App\Gnucash\Model\Commodity($c['ars'], 18000));
        $balance->add(new \App\Gnucash\Model\Commodity($c['ars'], 1670));

        $balance->add(new \App\Gnucash\Model\Commodity($c['usd'], 200));
        $balance->add(new \App\Gnucash\Model\Commodity($c['usd'], 17425, 100));
        $balance->add(new \App\Gnucash\Model\Commodity($c['usd'], 26));

        $balance->add(new \App\Gnucash\Model\Commodity($c['uyu'], 788249, 100));

        // var_dump($balance->getTotal());
        // var_dump($balance->getTotal($c['adf']));
        // var_dump($balance->exchange($c['ars'])->getTotal($c['ars']));
        // var_dump($balance->exchange($c['uyu'])->getTotal($c['uyu']));
        // var_dump($balance->exchange($c['usd'])->getTotal($c['usd']));

        var_dump($balance->exchange($c['usd'])->exchange($c['ars'])->getTotal($c['ars']));

        exit();


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

            $report = new PeriodTotalsByAccountTypeReport();

            $report->run()->each(function($balance, $accType) {
                var_dump($accType);
                var_dump($balance);
            });

            return;
            // var_dump($report->run()->groupBy('commodity')); exit();

            $report->run()->groupBy('commodity')->each(function($amounts, $commodity) {

                var_dump($commodity);

                $currency = new \App\Gnucash\Model\Currency($commodity);

                $amounts->each(function($amount) use ($currency) {
                    $currency->addAmount($amount->amount, $amount->fraction);
                });

                var_dump($currency);
            });

            return;

            return var_dump($periods->all());
        }

        return $validator->getErrors();
    }
}
