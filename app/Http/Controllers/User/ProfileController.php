<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
    $user = Auth::user();
        $resident = $user->resident;
        $activeReports = 0;
        $deliveredReports = 0;
        if ($resident) {
            $activeReports = \App\Models\Report::where('resident_id', $resident->id)
                ->whereHas('reportStatuses', function ($q) {
                    $q->whereIn('status', ['delivered', 'in_process']);
                })
                ->count();

            $deliveredReports = \App\Models\Report::where('resident_id', $resident->id)
                ->whereHas('reportStatuses', function ($q) {
                    $q->where('status', 'completed');
                })
                ->count();
        }
        return view('pages.app.profile', compact('activeReports', 'deliveredReports'));
    }
}
