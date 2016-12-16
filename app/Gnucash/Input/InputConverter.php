<?php

namespace App\Gnucash\Input;

use Exception;
use DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InputConverter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getAsCarbonDate($key, $default = null)
    {
        if ($input = $this->getInput($key, $default)) {
            try {
                return new Carbon($input);
            } catch (Exception $e) {}
        }
    }

    public function getAsDateInterval($key, $default = null)
    {
        if ($input = $this->getInput($key, $default)) {
            try {
                return new DateInterval($input);
            } catch (Exception $e) {}
        }
    }

    public function getInput($key, $default = null)
    {
        $value = $this->request->input($key);

        if ( ! is_null($value)) {
            return $value;
        }

        if ( ! is_null($default)) {
            return is_callable($default) ? $default() : $default;
        }
    }
}
