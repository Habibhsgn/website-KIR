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
            margin: 0;
            padding: 0;
        }

        /* Setiap halaman */
        .page {
            width: 21cm;
            min-height: 29.7cm;
            box-sizing: border-box;
            background-image: url('{{ public_path("assets/img/template_surat.png") }}');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: top center;
            padding: 4cm 2cm 2cm 2cm; /* top padding untuk kop */
            position: relative;
        }

        /* Pisahkan halaman */
        /* .page + .page {
            page-break-before: always;
        } */

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 15px;
        }

        .isi-surat p {
            text-align: justify;
            margin: 6px 0;
            line-height: 1.35;
        }

        table.data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 12px 0;
        }

        table.data-pasien td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        .tanda-tangan {
            width: 100%;
            text-align: right;
            margin-top: 20px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="judul-surat " style="margin-top:2cm;">SURAT KETERANGAN PEMERIKSAAN NAPZA</div>
        <div class="nomor-surat">Nomor: {{ $surat_bebas_narkoba->nomor_surat ?? '____________________' }}</div>

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
                <tr><td>Nama</td><td>:</td><td><b>{{ $pengajuan_surat->nama ?? '-' }}</b></td></tr>
                <tr><td>Tempat / Tanggal Lahir</td><td>:</td>
                    <td>{{ $pengajuan_surat->tempat_lahir ?? '-' }} /
                        {{ \Carbon\Carbon::parse($pengajuan_surat->tanggal_lahir ?? now())->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr><td>Pendidikan</td><td>:</td><td>{{ $pengajuan_surat->pendidikan ?? '-' }}</td></tr>
                <tr><td>Pekerjaan</td><td>:</td><td>{{ $pengajuan_surat->pekerjaan ?? '-' }}</td></tr>
                <tr><td>Status Pernikahan</td><td>:</td><td>{{ $pengajuan_surat->status ?? '-' }}</td></tr>
                <tr><td>Agama</td><td>:</td><td>{{ $pengajuan_surat->agama ?? '-' }}</td></tr>
                <tr><td>Alamat</td><td>:</td><td>{{ $alamat ?? '-' }}</td></tr>
            </table>

            <p>Berdasarkan hasil pemeriksaan:</p>
            <table class="data-pasien">
                <tr>
                    <td>Fisik Diagnostik*</td>
                    <td>:</td>
                    <td>{{ $surat_bebas_narkoba->fisik_diagnostik ? \Carbon\Carbon::parse($surat_bebas_narkoba->fisik_diagnostik)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Psikiatrik*</td>
                    <td>:</td>
                    <td>{{ $surat_bebas_narkoba->psikiatrik ? \Carbon\Carbon::parse($surat_bebas_narkoba->psikiatrik)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Pemeriksaan Tambahan*</td>
                    <td>:</td>
                    <td>{{ $surat_bebas_narkoba->pemeriksaan_tambahan ? \Carbon\Carbon::parse($surat_bebas_narkoba->pemeriksaan_tambahan)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
            </table>

            <p>Menunjukkan <b>{{ $surat_bebas_narkoba->tidak_ada_penyalahgunaan ? 'tidak ada' : 'ada' }}</b> gejala-gejala penggunaan narkotika / zat psikoaktif.</p>
            <p>Surat keterangan ini dibuat dengan sebenarnya untuk keperluan <b>{{ $pengajuan_surat->keperluan ?? '-' }}</b>.</p>
        </div>
        <div class="isi-surat">
            <p class="section-title" style="page-break-before: always; margin-top:6cm;">A. PEMERIKSAAN FISIK</p>
            <table class="data-pasien">
                <tr><td>1. Penampilan</td><td>:</td><td>{{ $surat_bebas_narkoba->penampilan ?? '-' }}</td></tr>
                <tr><td>2. Cara Berjalan</td><td>:</td><td>{{ $surat_bebas_narkoba->cara_berjalan ?? '-' }}</td></tr>
                <tr><td>3. Cara Bicara</td><td>:</td><td>{{ $surat_bebas_narkoba->cara_bicara ?? '-' }}</td></tr>
                <tr><td>4. Konjungtiva</td><td>:</td><td>{{ $surat_bebas_narkoba->konjungtiva ?? '-' }}</td></tr>
                <tr><td>5. Bekas Suntikan</td><td>:</td><td>{{ $surat_bebas_narkoba->bekas_suntikan ?? '-' }}</td></tr>
                <tr><td>6. Tremor</td><td>:</td><td>{{ $surat_bebas_narkoba->tremor ?? '-' }}</td></tr>
            </table>

            <p class="section-title">B. PEMERIKSAAN PSIKIATRIK</p>
            <table class="data-pasien">
                <tr><td>7. Alur Pembicaraan</td><td>:</td><td>{{ $surat_bebas_narkoba->alur_pembicaraan ?? '-' }}</td></tr>
                <tr><td>8. Waham</td><td>:</td><td>{{ $surat_bebas_narkoba->waham ?? '-' }}</td></tr>
                <tr><td>9. Halusinasi</td><td>:</td><td>{{ $surat_bebas_narkoba->halusinasi ?? '-' }}</td></tr>
                <tr><td>- Akustik</td><td>:</td><td>{{ $surat_bebas_narkoba->halusinasi_akustik ?? '-' }}</td></tr>
                <tr><td>- Visual</td><td>:</td><td>{{ $surat_bebas_narkoba->halusinasi_visual ?? '-' }}</td></tr>
                <tr><td>- Lain-lain</td><td>:</td><td>{{ $surat_bebas_narkoba->halusinasi_lain ?? '-' }}</td></tr>
            </table>

            <p class="section-title">C. PEMERIKSAAN TAMBAHAN</p>
            <table class="data-pasien">
                <tr><td>1. Cannabis / Ganja</td><td>:</td><td>{{ $surat_bebas_narkoba->cannabis ?? '-' }}</td></tr>
                <tr><td>2. Opiate / Opi</td><td>:</td><td>{{ $surat_bebas_narkoba->opiate ?? '-' }}</td></tr>
                <tr><td>3. Metamphetamine / Met</td><td>:</td><td>{{ $surat_bebas_narkoba->metamphetamine ?? '-' }}</td></tr>
                <tr><td>4. MDMA / Extacy</td><td>:</td><td>{{ $surat_bebas_narkoba->mdma ?? '-' }}</td></tr>
                <tr><td>5. Benzodiazepine</td><td>:</td><td>{{ $surat_bebas_narkoba->benzodiazepine ?? '-' }}</td></tr>
            </table>

            <div class="tanda-tangan">
                <p>Gunung Lagan, {{ \Carbon\Carbon::parse($surat_bebas_narkoba->created_at ?? now())->translatedFormat('d F Y') }}</p>
                <p>a.n Direktur RSUD Aceh Singkil</p>
                <p><strong>Dokter yang memeriksa</strong></p>
                <br><br><br>
                <p><b><u>{{ $dokter->nama ?? '-' }}</u></b></p>
                <p>NIP. {{ $dokter->nip ?? '-' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
