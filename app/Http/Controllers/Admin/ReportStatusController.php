<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportStatusRequest;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ReportStatusRepositoryInterface;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert as Swal;
use App\Notifications\ReportStatusChanged;
use App\Models\User;

class ReportStatusController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ReportStatusRepositoryInterface $reportStatusRepository;

    public function __construct(ReportRepositoryInterface $reportRepository, ReportStatusRepositoryInterface $reportStatusRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->reportStatusRepository = $reportStatusRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($reportId)
    {
        $report = $this->reportRepository->getReportById($reportId);
        
        return view('pages.admin.report-status.create', compact('report'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportStatusRequest $request)
    {
        $data = $request->validated();

        if ($request->image) {
            $data['image'] = $request->file('image')->store('assets/report-status/image', 'public');
        }

        $status = $this->reportStatusRepository->createReportStatus($data);

        // Notifikasi ke user pelapor
        $report = $this->reportRepository->getReportById($data['report_id']);
        if ($report && $report->resident && $report->resident->user) {
            $report->resident->user->notify(new ReportStatusChanged($report, $data['status'], $data['description'] ?? null));
        }
        // Notifikasi ke semua admin
        foreach (User::role('admin')->get() as $admin) {
            $admin->notify(new ReportStatusChanged($report, $data['status'], $data['description'] ?? null));
        }

        Swal::toast('Data progress laporan berhasil ditambahkan & notifikasi dikirim.', 'success')->timerProgressBar();

        return redirect()->route('admin.report.show', $request->report_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);

        return view('pages.admin.report-status.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportStatusRequest $request, string $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('assets/report-status/image', 'public');
        }

        $this->reportStatusRepository->updateReportStatus($data, $id);

        // Notifikasi ke user pelapor
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $report = $this->reportRepository->getReportById($status->report_id);
        if ($report && $report->resident && $report->resident->user) {
            $report->resident->user->notify(new ReportStatusChanged($report, $data['status'], $data['description'] ?? null));
        }
        // Notifikasi ke semua admin
        foreach (User::role('admin')->get() as $admin) {
            $admin->notify(new ReportStatusChanged($report, $data['status'], $data['description'] ?? null));
        }

        Swal::toast('Data status laporan berhasil diperbarui & notifikasi dikirim.', 'success')->timerProgressBar();

        return redirect()->route('admin.report.show', $request->report_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $status = $this->reportStatusRepository->getReportStatusById($id);

        $this->reportStatusRepository->deleteReportStatus($id);

        Swal::toast('Data status laporan berhasil dihapus.', 'success')->timerProgressBar();

        return redirect()->route('admin.report.show', $status->report_id);
    }
}
