<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Pemeriksaan NAPZA</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            background-image: url('{{ asset('assets/img/template_surat.png') }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: top center;
            padding: 3.3cm 2.2cm 2cm;
        }

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 100px;
            margin-bottom: 0.3cm;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 0.8cm;
        }

        .isi-surat {
            line-height: 1.45;
            text-align: justify;
        }

        table.data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.6cm;
        }

        table.data-pasien td {
            padding: 1.5px 3px;
            vertical-align: top;
        }

        .tanda-tangan {
            margin-top: 1.8cm;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 50%;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .section-title {
            font-weight: bold;
            margin-top: 0.5cm;
            margin-bottom: 0.2cm;
        }

        /* Untuk cetak */
        @media print {
            body {
                margin: 0;
                background: none;
            }
            .page {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    {{-- ================= HALAMAN 1 ================= --}}
    <div class="page">
        <div class="judul-surat">SURAT KETERANGAN PEMERIKSAAN NAPZA</div>
        <div class="nomor-surat">Nomor: {{ $surat->nomor_surat }}</div>

        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini:</p>
            <table class="data-pasien">
                <tr><td>Nama</td><td>:</td><td>{{ $dokter->nama ?? '-' }}</td></tr>
                <tr><td>NIP</td><td>:</td><td>{{ $dokter->nip ?? '-' }}</td></tr>
                <tr><td>Jabatan</td><td>:</td><td>{{ $dokter->jabatan ?? '-' }}</td></tr>
                <tr><td>Instansi</td><td>:</td><td>{{ $dokter->instansi ?? 'RSUD Aceh Singkil' }}</td></tr>
            </table>

            <p>Telah melakukan Pemeriksaan NAPZA terhadap:</p>
            <table class="data-pasien">
                <tr><td>Nama</td><td>:</td><td>{{ $pengajuan->nama }}</td></tr>
                <tr><td>Tempat / Tanggal Lahir</td><td>:</td><td>{{ $pengajuan->tempat_lahir }} / {{ \Carbon\Carbon::parse($pengajuan->tanggal_lahir)->translatedFormat('d F Y') }}</td></tr>
                <tr><td>Pendidikan</td><td>:</td><td>{{ $pengajuan->pendidikan ?? '-' }}</td></tr>
                <tr><td>Pekerjaan</td><td>:</td><td>{{ $pengajuan->pekerjaan }}</td></tr>
                <tr><td>Status Pernikahan</td><td>:</td><td>{{ $pengajuan->status ?? '-' }}</td></tr>
                <tr><td>Agama</td><td>:</td><td>{{ $pengajuan->agama }}</td></tr>
                <tr><td>Alamat</td><td>:</td><td>{{ $alamat }}</td></tr>
            </table>

            <p>Berdasarkan hasil pemeriksaan:</p>
            <table class="data-pasien">
                <tr><td>Fisik Diagnostik*</td><td>:</td><td>Pada tanggal {{ $surat->fisik_diagnostik ? \Carbon\Carbon::parse($surat->fisik_diagnostik)->translatedFormat('d F Y') : '-' }}</td></tr>
                <tr><td>Psikiatrik*</td><td>:</td><td>Pada tanggal {{ $surat->psikiatrik ? \Carbon\Carbon::parse($surat->psikiatrik)->translatedFormat('d F Y') : '-' }}</td></tr>
                <tr><td>Pemeriksaan Tambahan*</td><td>:</td><td>Pada tanggal {{ $surat->pemeriksaan_tambahan ? \Carbon\Carbon::parse($surat->pemeriksaan_tambahan)->translatedFormat('d F Y') : '-' }}</td></tr>
            </table>

            <p>Menunjukkan <b>{{ $surat->tidak_ada_penyalahgunaan ? 'tidak ada' : 'ada' }}</b> gejala-gejala penggunaan narkotika / zat psikoaktif.</p>
            <p>Surat keterangan ini dibuat dengan sebenarnya untuk keperluan <b>{{ $pengajuan->keperluan }}</b>.</p>

            
        </div>
    </div>

    {{-- ================= HALAMAN 2 ================= --}}
    <br>
    <div class="page page-break">
        <div class="isi-surat">
            <br><br>
            <p class="section-title">A. PEMERIKSAAN FISIK</p>
            <table class="data-pasien">
                <tr><td>1. Penampilan</td><td>:</td><td>{{ $surat->penampilan }}</td></tr>
                <tr><td>2. Cara Berjalan</td><td>:</td><td>{{ $surat->cara_berjalan }}</td></tr>
                <tr><td>3. Cara Bicara</td><td>:</td><td>{{ $surat->cara_bicara }}</td></tr>
                <tr><td>4. Konjungtiva</td><td>:</td><td>{{ $surat->konjungtiva }}</td></tr>
                <tr><td>5. Bekas Suntikan</td><td>:</td><td>{{ $surat->bekas_suntikan }}</td></tr>
                <tr><td>6. Tremor</td><td>:</td><td>{{ $surat->tremor }}</td></tr>
            </table>

            <p class="section-title">B. PEMERIKSAAN PSIKIATRIK</p>
            <table class="data-pasien">
                <tr><td>7. Alur Pembicaraan</td><td>:</td><td>{{ $surat->alur_pembicaraan }}</td></tr>
                <tr><td>8. Waham</td><td>:</td><td>{{ $surat->waham }}</td></tr>
                <tr><td>9. Halusinasi</td><td>:</td><td>{{ $surat->halusinasi }}</td></tr>
                <tr><td>- Akustik</td><td>:</td><td>{{ $surat->halusinasi_akustik }}</td></tr>
                <tr><td>- Visual</td><td>:</td><td>{{ $surat->halusinasi_visual }}</td></tr>
                <tr><td>- Lain-lain</td><td>:</td><td>{{ $surat->halusinasi_lain }}</td></tr>
            </table>

            <p class="section-title">C. PEMERIKSAAN TAMBAHAN</p>
            <table class="data-pasien">
                <tr><td>1. Cannabis / Ganja</td><td>:</td><td>{{ $surat->cannabis }}</td></tr>
                <tr><td>2. Opiate / Opi</td><td>:</td><td>{{ $surat->opiate }}</td></tr>
                <tr><td>3. Metamphetamine / Met</td><td>:</td><td>{{ $surat->metamphetamine }}</td></tr>
                <tr><td>4. MDMA / Extacy</td><td>:</td><td>{{ $surat->mdma }}</td></tr>
                <tr><td>5. Benzodiazepine</td><td>:</td><td>{{ $surat->benzodiazepine }}</td></tr>
            </table>

            <div class="tanda-tangan">
                <div class="ttd-box">
                    <p>Gunung Lagan, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</p>
                    <p>a.n Direktur RSUD Aceh Singkil<br>Dokter yang memeriksa</p>
                    <br><br><br>
                    <p><b><u>{{ $dokter->nama ?? '' }}</u></b><br>NIP. {{ $dokter->nip ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
