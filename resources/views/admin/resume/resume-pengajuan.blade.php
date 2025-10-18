@extends('layouts.admin')

@section('title', 'Data Pengajuan Pasien')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Daftar Pasien Pengajuan</h4>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Pasien</th>
                    <th>Total Surat</th>
                    <th>Tanggal Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pasien as $p)
                    <tr>
                        <td>{{ $p->nik }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->total_surat }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->terakhir)->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('resume.show', $p->nik) }}" class="btn btn-sm btn-primary">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
