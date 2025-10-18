@extends('layouts.admin')

@section('title', 'Form Surat Bebas Narkoba')

@section('content')
<style>
    select option {
        color: #000;
    }
</style>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Form Surat Bebas Narkoba</h4>

        <form action="{{ route('surat-narkoba.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
            <input type="hidden" name="nama_pasien" value="{{ $pengajuan->nama }}">

            {{-- 0. Nomor Surat --}}
            <div class="mb-3">
                <label>Nomor Surat</label>
                <input type="text" name="nomor_surat" class="form-control" placeholder="Masukkan Nomor Surat" required>
            </div>

            {{-- 1. Dokter --}}
            <div class="mb-3">
                <label>Dokter Pemeriksa</label>
                <select name="dokter_id" class="form-select" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($dokter as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }} - {{ $d->jabatan }} ({{ $d->instansi }})</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Data Pasien --}}
            <h5 class="mt-4">Data Pasien</h5>
            <ul>
                <li><strong>Nama:</strong> {{ $pengajuan->nama }}</li>
                <li><strong>TTL:</strong> {{ $pengajuan->tempat_lahir }}, {{ $pengajuan->tanggal_lahir }}</li>
                <li><strong>Agama:</strong> {{ $pengajuan->agama }}</li>
                <li><strong>Pekerjaan:</strong> {{ $pengajuan->pekerjaan }}</li>
                <li><strong>Alamat:</strong> {{ $alamat ?? '-' }}</li>
            </ul>

            {{-- 3. Jadwal Pemeriksaan --}}
            <h5 class="mt-4">Jadwal Pemeriksaan</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Fisik Diagnostik</label>
                    <input type="datetime-local" name="fisik_diagnostik" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Psikiatrik</label>
                    <input type="datetime-local" name="psikiatrik" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Pemeriksaan Tambahan</label>
                    <input type="datetime-local" name="pemeriksaan_tambahan" class="form-control">
                </div>
            </div>

            {{-- 4. Pemeriksaan Fisik --}}
            <h5 class="mt-4">Pemeriksaan Fisik</h5>
            @php
                $fisikFields = [
                    'penampilan' => ['Rapi', 'Kurang Rapi', 'Tidak Rapi'],
                    'cara_berjalan' => ['Biasa', 'Pincang', 'Tidak Normal'],
                    'cara_bicara' => ['Biasa', 'Gagap', 'Tidak Jelas'],
                    'konjungtiva' => ['Normal', 'Anemis', 'Pucat'],
                    'bekas_suntikan' => ['Tidak Ada', 'Ada di Lengan', 'Ada di Bagian Lain'],
                    'tremor' => ['Tidak Ada', 'Ringan', 'Sedang', 'Berat'],
                ];
            @endphp
            <div class="row">
                @foreach ($fisikFields as $field => $options)
                    <div class="col-md-4 mb-3">
                        <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                        <select name="{{ $field }}" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach ($options as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            {{-- 5. Pemeriksaan Psikiatrik --}}
            <h5 class="mt-4">Pemeriksaan Psikiatrik</h5>
            @php
                $psikiatrikFields = [
                    'alur_pembicaraan' => ['Normal', 'Lompat-lompat', 'Tidak Koheren'],
                    'waham' => ['Tidak Ada', 'Persekusi', 'Kebesaran Diri', 'Curiga Berlebihan'],
                    'halusinasi' => ['Tidak Ada', 'Ada'],
                ];
            @endphp
            <div class="row">
                @foreach ($psikiatrikFields as $field => $options)
                    <div class="col-md-4 mb-3">
                        <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                        <select name="{{ $field }}" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach ($options as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            {{-- Detail Halusinasi --}}
            <div class="row">
                @foreach (['akustik', 'visual', 'lain'] as $type)
                    <div class="col-md-4 mb-3">
                        <label>Halusinasi {{ ucwords($type) }}</label>
                        <select name="halusinasi_{{ $type }}" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Ada">Tidak Ada</option>
                            <option value="Ada">Ada</option>
                        </select>
                    </div>
                @endforeach
            </div>

            {{-- 6. Pemeriksaan Tambahan (Tes Narkoba) --}}
            <h5 class="mt-4">Pemeriksaan Tambahan (Tes Narkoba)</h5>
            @php
                $tambahanFields = [
                    'cannabis' => 'Cannabis / Ganja',
                    'opiate' => 'Opiate / Opi',
                    'metamphetamine' => 'Metamphetamine / Met',
                    'mdma' => 'MDMA / Ekstasi',
                    'benzodiazepine' => 'Benzodiazepine',
                ];
            @endphp
            <div class="row">
                @foreach ($tambahanFields as $field => $label)
                    <div class="col-md-4 mb-3">
                        <label>{{ $label }}</label>
                        <select name="{{ $field }}" class="form-select">
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Negatif">Negatif</option>
                            <option value="Positif">Positif</option>
                            <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                        </select>
                    </div>
                @endforeach

                <div class="col-md-6 mb-3">
                    <input type="checkbox" name="tidak_ada_penyalahgunaan" class="form-check-input" id="tidak_ada">
                    <label for="tidak_ada" class="form-check-label">
                        Menunjukkan Tidak Ada Penyalahgunaan Narkoba
                    </label>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Simpan Surat</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
