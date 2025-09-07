<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController extends Controller
{

    public function index(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $query = Report::with(['reportCategory', 'resident.user', 'reportStatuses']);
        if ($start && $end) {
            $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        } elseif ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        $reports = $query->orderBy('created_at', 'desc')->get();
    return view('pages.admin.report-export.index', compact('reports'));
    }


    public function exportExcel(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        return Excel::download(new ReportsExport($start, $end), 'laporan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $query = Report::with(['reportCategory', 'resident.user', 'reportStatuses']);
        if ($start && $end) {
            $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        } elseif ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        $reports = $query->orderBy('created_at', 'desc')->get();
    $pdf = PDF::loadView('pages.admin.report-export.pdf', compact('reports'));
        return $pdf->download('laporan.pdf');
    }
}
