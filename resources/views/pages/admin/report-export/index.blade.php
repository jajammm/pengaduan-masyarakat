@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Export Data Laporan</h1>
    <form method="GET" action="" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="start_date" class="mr-2">Start Date:</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="form-group mr-2">
            <label for="end_date" class="mr-2">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <button type="submit" class="btn btn-primary mr-2">Apply</button>
        <a href="{{ route('admin.report.export.excel', array_merge(request()->except('page'))) }}" class="btn btn-success mr-2">Export Excel</a>
        <a href="{{ route('admin.report.export.pdf', array_merge(request()->except('page'))) }}" class="btn btn-danger">Export PDF</a>
    </form>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="exportTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Laporan</th>
                            <th>Judul Laporan</th>
                            <th>Kategori Laporan</th>
                            <th>Lokasi Laporan</th>
                            <th>Yang Melapor</th>
                            <th>Tanggal Melapor</th>
                            <th>Tanggal Laporan Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $i => $report)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $report->code }}</td>
                            <td>{{ $report->title }}</td>
                            <td>{{ $report->reportCategory->name ?? '-' }}</td>
                            <td>{{ $report->address }}</td>
                            <td>{{ $report->resident->user->name ?? '-' }}</td>
                            <td>{{ $report->created_at ? $report->created_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ optional($report->reportStatuses->where('status','completed')->sortByDesc('created_at')->first())->created_at ? optional($report->reportStatuses->where('status','completed')->sortByDesc('created_at')->first())->created_at->format('Y-m-d H:i') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#exportTable').DataTable({
            "order": [[ 0, "asc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
    });
</script>
@endsection
