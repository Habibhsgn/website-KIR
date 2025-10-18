<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kesehatan</title>
    <style>
        /* Ukuran halaman A4 dan margin 0 agar background penuh */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            width: 21cm;
            /* Hapus height: 29.7cm; biarkan auto */
            background-image: url('{{ public_path('assets/img/template_surat.png') }}');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: center top;
            overflow: hidden;
        }

        .surat-container {
            position: relative;
            width: 18cm;
            /* kurangi tinggi agar total < 29.7cm */
            height: auto;
            margin: 3.2cm auto 1cm;
            /* total aman ~28.5cm */
            padding: 2cm 2cm 1.2cm 2cm;
            box-sizing: border-box;
        }

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 8px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 15px;
        }

        .isi-surat {
            line-height: 1.35;
            text-align: justify;
        }

        table.data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data-pasien td {
            padding: 1px 3px;
            vertical-align: top;
        }

        .tanda-tangan {
            text-align: right;
            margin-top: 15px;
        }

        .tanda-tangan p {
            margin: 2px 0;
        }

        .catatan {
            border-top: 1px solid #000;
            margin-top: 8px;
            padding-top: 3px;
            font-size: 11pt;
            line-height: 1.2;
        }

        .catatan ul {
            margin: 3px 0 0 20px;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="surat-container">
        <div class="judul-surat">SURAT KETERANGAN KESEHATAN</div>
        <div class="nomor-surat">NOMOR: {{ $surat_kesehatan->nomor_surat ?? '____________________' }}</div>

        <div class="isi-surat">
            <p>
                Yang bertanda tangan di bawah ini, <strong>{{ $dokter->nama ?? 'Nama Dokter' }}</strong>,
                {{ $dokter->jabatan ?? 'Jabatan' }} pada {{ $dokter->instansi ?? 'Rumah Sakit' }},
                menerangkan bahwa:
            </p>

            <table class="data-pasien">
                <tr>
                    <td width="150">Nama</td>
                    <td width="10">:</td>
                    <td>{{ $pengajuan_surat->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tempat / Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->tempat_lahir ?? '-' }},
                        {{ \Carbon\Carbon::parse($pengajuan_surat->tanggal_lahir ?? now())->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->jenis_kelamin ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->agama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $alamat ?? '-' }}</td>
                </tr>
            </table>

            <p><strong>{{ $surat_kesehatan->isi_keterangan ?? '______________________' }}</strong></p>

            <p style="text-align:center; font-weight:bold; margin:18px 0;">
                {{ $pengajuan_surat->keperluan ?? '____________________________' }}
            </p>

            <p>
                Demikian Surat Keterangan Kesehatan ini dibuat dengan sebenarnya agar dapat dipergunakan seperlunya.
            </p>
        </div>

        <div class="tanda-tangan">
            <p>Gunung Lagan,
                {{ \Carbon\Carbon::parse($surat_kesehatan->tanggal_pemeriksaan ?? now())->translatedFormat('d F Y') }}</p>
            <p>a.n Direktur RSUD Aceh Singkil</p>
            <p><strong>Dokter yang memeriksa</strong></p><br><br><br>
            <p style="text-decoration:underline; font-weight:bold;">{{ $dokter->nama ?? 'Nama Dokter' }}</p>
            <p>NIP. {{ $dokter->nip ?? 'NIP Dokter' }}</p>
        </div>

        <div class="catatan">
            <p style="font-weight:bold;">Catatan:</p>
            <ul>
                <li>Tinggi Badan: {{ $surat_kesehatan->tinggi_badan ?? '___' }} cm</li>
                <li>Berat Badan: {{ $surat_kesehatan->berat_badan ?? '___' }} kg</li>
                <li>Tensi Darah: {{ $surat_kesehatan->tensi ?? '___' }} mmHg</li>
                <li>Golongan Darah: {{ $surat_kesehatan->gol_darah ?? '___' }}</li>
            </ul>
        </div>
    </div>
</body>

</html>
