<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Export PDF Laporan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            font-size: 12px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Data Laporan</h2>
    <p>
        Periode:
        @if(request('start_date') && request('end_date'))
            {{ request('start_date') }} s/d {{ request('end_date') }}
        @elseif(request('start_date'))
            Mulai {{ request('start_date') }}
        @elseif(request('end_date'))
            Hingga {{ request('end_date') }}
        @else
            Semua Data
        @endif
    </p>
    <table>
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
</body>

</html>