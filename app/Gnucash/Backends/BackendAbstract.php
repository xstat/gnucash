<?php

namespace App\Gnucash\Backends;

use App\Gnucash\Model\Period;

abstract class BackendAbstract implements BackendInterface
{
    protected $options = [
        'period' => false,
        'betweenSameAccountTypesOnly' => false
    ];

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function getOption($option)
    {
        return @ $this->options[$option];
    }

    public function clearOption($option)
    {
        unset($this->options[$option]);
    }
}
