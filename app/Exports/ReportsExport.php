<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date;
    protected $end_date;
    protected $status;
    protected $category;

    public function __construct($start_date = null, $end_date = null, $status = null, $category = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->category = $category;
    }

    public function collection()
    {
        $query = Report::with(['reportCategory', 'resident.user', 'reportStatuses']);
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59']);
        } elseif ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        } elseif ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }
        if ($this->category) {
            $query->where('report_category_id', $this->category);
        }
        if ($this->status) {
            $query->whereHas('reportStatuses', function($q) {
                $q->where('status', $this->status);
            });
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function map($report): array
    {
        $latestStatus = $report->reportStatuses->sortByDesc('created_at')->first();
        return [
            '', // No, diisi di Excel
            $report->code,
            $report->title,
            $report->reportCategory->name ?? '-',
            $report->address,
            $report->resident->user->name ?? '-',
            $latestStatus ? __(ucfirst(str_replace('_', ' ', $latestStatus->status))) : '-',
            $report->created_at ? $report->created_at->format('Y-m-d H:i') : '-',
            $report->reportStatuses()->where('status', 'completed')->orderByDesc('created_at')->value('created_at')
                ? $report->reportStatuses()->where('status', 'completed')->orderByDesc('created_at')->value('created_at')->format('Y-m-d H:i') : '-',
        ];
    }

    public function headings(): array
    {
        $periode = 'Semua Data';
        if ($this->start_date && $this->end_date) {
            $periode = 'Periode: ' . $this->start_date . ' s/d ' . $this->end_date;
        } elseif ($this->start_date) {
            $periode = 'Mulai ' . $this->start_date;
        } elseif ($this->end_date) {
            $periode = 'Hingga ' . $this->end_date;
        }
        return [
            [$periode],
            ['No', 'Kode Laporan', 'Judul Laporan', 'Kategori Laporan', 'Lokasi Laporan', 'Pelapor', 'Status', 'Tanggal Melapor', 'Laporan Selesai'],
        ];
    }
}
