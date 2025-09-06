@extends('layouts.admin')

@section('title', 'Detail Laporan')

@section('content')
    <a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <td>Kode Laporan</td>
                    <td>{{ $report->code }}</td>
                </tr>
                <tr>
                    <td>Pelapor</td>
                    <td>{{ $report->resident->user->email }} - {{ $report->resident->user->name }}</td>
                </tr>
                <tr>
                    <td>Kategori Laporan</td>
                    <td>{{ $report->reportCategory->name }}</td>
                </tr>
                <tr>
                    <td>Judul Laporan</td>
                    <td>{{ $report->title }}</td>
                </tr>
                <tr>
                    <td>Deskripsi Laporan</td>
                    <td>{{ $report->description }}</td>
                </tr>
                <tr>
                    <td>Bukti Laporan</td>
                    <td>
                        @if ($report->image)
                            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}"
                                style="width: 150px;">
                        @else
                            <span>Tidak ada gambar</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Latitude</td>
                    <td>{{ $report->latitude }}</td>
                </tr>
                <tr>
                    <td>Longitude</td>
                    <td>{{ $report->longitude }}</td>
                </tr>
                <tr>
                    <td>Map View</td>
                    <td>
                        <div id="map" style="width: 100%; height: 300px;"></div>
                    </td>
                </tr>
                <tr>
                    <td>Lokasi Laporan</td>
                    <td>{{ $report->address }}</td>
                </tr>

            </table>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Laporan</h6>
        </div>
        <div class="card-body">
            <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-primary mb-3">Tambah Progress
                Laporan</a>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->reportStatuses as $status)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($status->image)
                                        <img src="{{ asset('storage/' . $status->image) }}" alt="Icon" width="100">
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $status->status }}
                                </td>
                                <td>{{ $status->description }}</td>
                                <td>
                                    <a href="{{ route('admin.report-status.edit', $status->id) }}"
                                        class="btn btn-warning">Edit</a>



                                    <form action="{{ route('admin.report-status.destroy', $status->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var mymap = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 13);

        var marker = L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(mymap);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(mymap);

        marker.bindPopup("<b>Lokasi Laporan</b><br />berada di {{ $report->address }}").openPopup();
    </script>
@endsection