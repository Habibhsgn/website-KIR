@extends('layouts.admin')

@section('title', 'Isi Surat Kesehatan')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Isi Surat Kesehatan</h4>

        <form action="{{ route('surat-kesehatan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
            <input type="hidden" name="nama_pasien" value="{{ $pengajuan->nama }}">

            {{-- Nomor Surat --}}
            <div class="mb-3">
                <label for="nomor_surat" class="form-label">Nomor Surat</label>
                <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" placeholder="Masukkan Nomor Surat" required>
            </div>

            <div class="mb-3">
                <label for="dokter_id" class="form-label">Dokter Pemeriksa</label>
                <select name="dokter_id" id="dokter_id" class="form-select" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($dokter as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->jabatan }} - {{ $d->instansi }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Data Pasien</label>
                <div class="border p-3 rounded bg-light">
                    <p><strong>Nama:</strong> {{ $pengajuan->nama }}</p>
                    <p><strong>TTL:</strong> {{ $pengajuan->tempat_lahir ?? '-' }}, {{ $pengajuan->tanggal_lahir ?? '-' }}</p>
                    <p><strong>Agama:</strong> {{ $pengajuan->agama ?? '-' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $pengajuan->pekerjaan ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $alamat ?? '-' }}</p>
                </div>
            </div>

            <div class="mb-3">
                <label for="hasil" class="form-label">Hasil Pemeriksaan</label>
                <select name="hasil" id="hasil" class="form-select" required>
                    <option value="">-- Pilih Hasil Pemeriksaan --</option>
                    <option value="BAIK dan SEHAT">BAIK dan SEHAT</option>
                    <option value="PERLU PEMANTAUAN">PERLU PEMANTAUAN</option>
                    <option value="TIDAK SEHAT">TIDAK SEHAT</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tanggal_pemeriksaan" class="form-label">Tanggal Pemeriksaan</label>
                <input type="date" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan Surat</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
