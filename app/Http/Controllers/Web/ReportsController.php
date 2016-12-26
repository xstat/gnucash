<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Gnucash\Services\ReportService;

class ReportsController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('reports.index', [
            'reports' => $this->reportService->all()
        ]);
    }

    public function view(Request $request, $reportName)
    {
        $report = $this->reportService->load($reportName);

        if (is_null($report)) {
            abort(404);
        }

        return view('reports.view', ['report' => $report]);
    }

    public function action($reportName, $reportAction)
    {
        $report = $this->reportService->load($reportName);

        if ($report && method_exists($report, $reportAction)) {
            return $report->$reportAction();
        }

        abort(404);
    }
}
