@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Export Data Laporan</h1>
        <form method="GET" action="" class="form-inline mb-3">
            <div class="form-group mr-2">
                <label for="start_date" class="mr-2">Start Date:</label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                    value="{{ request('start_date') }}">
            </div>
            <div class="form-group mr-2">
                <label for="end_date" class="mr-2">End Date:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="form-group mr-2">
                <label for="status" class="mr-2">Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Dikirim</option>
                    <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="form-group mr-2">
                <label for="category" class="mr-2">Kategori:</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mr-2">Apply</button>
            <a href="{{ route('admin.report.export.excel', array_merge(request()->except('page'))) }}"
                class="btn btn-success mr-2">Export Excel</a>
            <a href="{{ route('admin.report.export.pdf', array_merge(request()->except('page'))) }}"
                class="btn btn-danger">Export PDF</a>
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
                                <th>Pelapor</th>
                                <th>Status</th>
                                <th>Tanggal Melapor</th>
                                <th>Laporan Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $i => $report)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $report->code }}</td>
                                    <td>{{ $report->title }}</td>
                                    <td>{{ $report->reportCategory->name ?? '-' }}</td>
                                    <td>{{ $report->address }}</td>
                                    <td>{{ $report->resident->user->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $latestStatus = $report->reportStatuses->sortByDesc('created_at')->first();
                                        @endphp
                                        {{ $latestStatus ? __(ucfirst(str_replace('_', ' ', $latestStatus->status))) : '-' }}
                                    </td>
                                    <td>{{ $report->created_at ? $report->created_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ optional($report->reportStatuses->where('status', 'completed')->sortByDesc('created_at')->first())->created_at ? optional($report->reportStatuses->where('status', 'completed')->sortByDesc('created_at')->first())->created_at->format('Y-m-d H:i') : '-' }}
                                    </td>
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
        $(document).ready(function () {
            $('#exportTable').DataTable({
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
        });
    </script>
@endsection