<?php

namespace App\Repositories;

use App\Interfaces\ReportStatusRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportStatus;
use App\Models\Resident;
use App\Models\User;

class ReportStatusRepository implements ReportStatusRepositoryInterface
{
    public function getAllReportStatuses()
    {
        return ReportStatus::all();
    }

    public function getReportStatusById(int $id)
    {
        return ReportStatus::where('id', $id)->first();
    }


    public function createReportStatus(array $data)
    {
        return ReportStatus::create($data);
    }

    public function updateReportStatus(array $data, int $id)
    {
        $report = $this->getReportStatusById($id);

        return $report->update($data);
    }

    public function deleteReportStatus(int $id)
    {
        $report = $this->getReportStatusById($id);

        return $report->delete();
    }
}