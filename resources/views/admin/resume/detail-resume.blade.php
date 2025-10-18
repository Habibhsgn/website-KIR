@extends('layouts.admin')

@section('title', 'Detail Surat Pasien')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Detail Surat: {{ $nama }}</h4>

            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Surat</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $pengajuan)
                        @foreach ($pengajuan->suratStatus as $surat)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $surat['jenis'])) }}</td>
                                <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    @if ($surat['isFilled'])
                                        <span class="badge bg-success">Sudah Diisi</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum Diisi</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (!$surat['isFilled'] && $surat['routeCreate'])
                                        <a href="{{ $surat['routeCreate'] }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-pencil"></i> Isi Surat
                                        </a>
                                    @elseif ($surat['isFilled'])
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ $surat['previewRoute'] }}" target="_blank"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Preview
                                            </a>

                                            @if ($surat['pdfPath'])
                                                <a href="{{ asset('storage/' . $surat['pdfPath']) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-pdf"></i> Lihat PDF
                                                </a>
                                            @else
                                                <a href="{{ $surat['pdfRoute'] }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-gear"></i> Generate PDF
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Form belum tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if ($pengajuan->no_hp)
                            <form action="{{ route('resume.send-wa', $pengajuan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Kirim Surat ke WhatsApp</button>
                            </form>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif
    </div>
@endsection
