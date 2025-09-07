<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController extends Controller
{

    public function index(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $status = $request->get('status');
        $category = $request->get('category');
        $categories = ReportCategory::all();
        $query = Report::with(['reportCategory', 'resident.user', 'reportStatuses']);
        if ($start && $end) {
            $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        } elseif ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        if ($category) {
            $query->where('report_category_id', $category);
        }
        if ($status) {
            $query->whereHas('reportStatuses', function($q) use ($status) {
                $q->where('status', $status);
            });
        }
        $reports = $query->orderBy('created_at', 'desc')->get();
        return view('pages.admin.report-export.index', compact('reports', 'categories'));
    }


    public function exportExcel(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $status = $request->get('status');
        $category = $request->get('category');
        return Excel::download(new ReportsExport($start, $end, $status, $category), 'laporan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $status = $request->get('status');
        $category = $request->get('category');
        $query = Report::with(['reportCategory', 'resident.user', 'reportStatuses']);
        if ($start && $end) {
            $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        } elseif ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        if ($category) {
            $query->where('report_category_id', $category);
        }
        if ($status) {
            $query->whereHas('reportStatuses', function($q) use ($status) {
                $q->where('status', $status);
            });
        }
        $reports = $query->orderBy('created_at', 'desc')->get();
        $pdf = PDF::loadView('pages.admin.report-export.pdf', compact('reports'));
        return $pdf->download('laporan.pdf');
    }
}
