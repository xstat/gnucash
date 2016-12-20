<?php

namespace App\Gnucash\Reports;

use Illuminate\Support\Collection;

abstract class Report
{
    protected $id;
    protected $settings;

    public function __construct($id, Collection $settings)
    {
        $this->id = $id;
        $this->settings = $settings;
    }

    public function getTitle()
    {
        return $this->settings->get('title', $this->id);
    }

    public function getVueComponentName()
    {
        return sprintf('<%s></%s>', $this->id, $this->id);
    }
}
