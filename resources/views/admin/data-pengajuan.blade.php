@extends('layouts.admin')

@section('title', 'Data Pasien Pengajuan')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Data Pengajuan Surat</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jenis Surat</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status Pembayaran</th>
                        <th>Tanggal Kunjungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPengajuan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama ?? '-' }}</td>
                            <td>
                                @foreach ($item->jenis_surat_array as $jenis)
                                    <span class="badge badge-info me-1">
                                        {{ ucwords(str_replace('_', ' ', $jenis)) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                            <td>
                                @if (strtoupper($item->payment_status) === 'PENDING')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif (strtoupper($item->payment_status) === 'SETTLEMENT')
                                    <span class="badge badge-success">Sudah Dibayar</span>
                                @elseif (strtoupper($item->payment_status) === 'FAILED')
                                    <span class="badge badge-danger">Gagal</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($item->payment_status ?? 'Tidak Diketahui') }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kuota)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada pengajuan surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
