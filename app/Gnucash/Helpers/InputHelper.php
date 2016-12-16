<?php

namespace App\Gnucash\Helpers;

use Exception;
use DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InputHelper
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getDate($key, $default = null)
    {
        $date = null;

        try {

            $date = new Carbon(
                $this->request->input($key, $default)
            );

        } catch (Exception $e) {

            if ($default) {
                $date = new Carbon($default);
            }
        }

        return $date;
    }

    public function getDateInterval($key, $default = null)
    {
        $interval = null;

        try {

            $interval = new DateInterval(
                $this->request->Input($key, $default)
            );

        } catch (Exception $e) {

            if ($default) {
                $interval = new DateInterval($default);
            }
        }

        return $interval;
    }
}
