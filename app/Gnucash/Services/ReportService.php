<?php

namespace App\Gnucash\Services;

class ReportService
{
    protected $reports;

    public function __construct()
    {
        $this->reports = collect(config('gnucash.reports'));
    }

    public function all()
    {
        return $this->reports;
    }

    public function load($reportId)
    {
        $reportDefinition = collect($this->reports->get($reportId));
        $reportClass = $reportDefinition->get('class');

        if ($reportClass && class_exists($reportClass)) {
            return new $reportClass($reportId, $reportDefinition);
        }
    }
}
