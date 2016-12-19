<?php

namespace App\Gnucash\Reports;

use Illuminate\Support\Collection;

abstract class Report
{
    const TEMPLATE_PREFIX = 'reports.custom.';

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

    public function getViewName()
    {
        $defaultName = static::TEMPLATE_PREFIX . $this->id;
        return $this->settings->get('view', $defaultName);
    }
}