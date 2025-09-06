<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index(Request $request)
   {
        $filter = $request->get('filter', '7days'); // '7days', 'month', 'year'
        $labels = [];
        $data = [];

        if ($filter === '7days') {
            $start = Carbon::now()->subDays(6);
            $reports = Report::whereDate('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');

            // Build labels for last 7 days
            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i)->format('Y-m-d');
                $labels[] = $date;
                $data[] = $reports[$date] ?? 0;
            }
        } elseif ($filter === 'month') {
            $reports = Report::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('F');
                $data[] = $reports[$i] ?? 0;
            }
        } elseif ($filter === 'year') {
            $startYear = now()->year - 4;
            $reports = Report::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->whereYear('created_at', '>=', $startYear)
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year');

            for ($i = $startYear; $i <= now()->year; $i++) {
                $labels[] = $i;
                $data[] = $reports[$i] ?? 0;
            }
        }

        // Pie chart: laporan per kategori
        $categoryPie = ReportCategory::withCount('reports')->get();
        $pieLabels = $categoryPie->pluck('name');
        $pieData = $categoryPie->pluck('reports_count');

        // Top 5 kategori laporan terbanyak
        $topCategories = ReportCategory::withCount('reports')
            ->orderByDesc('reports_count')
            ->take(5)
            ->get();

        // Top 5 user dengan laporan terbanyak
        $topUsers = User::whereHas('resident.reports')
            ->with(['resident' => function($q) {
                $q->withCount('reports');
            }])
            ->get()
            ->sortByDesc(function($user) {
                return $user->resident->reports_count ?? 0;
            })
            ->take(5);

        return view('pages.admin.dashboard', compact(
            'labels', 'data', 'filter',
            'pieLabels', 'pieData',
            'topCategories', 'topUsers'
        ));

    }
}