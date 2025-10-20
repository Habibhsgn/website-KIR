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
            width: 21cm;
            background-image: url('{{ public_path('assets/img/template_surat.png') }}');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: center top;
        }

        .surat-container {
            position: relative;
            width: 18cm;
            margin: 3.4cm auto 1cm;
            /* posisi konten pas di bawah kop surat */
            padding: 0 1cm 1cm 1cm;
            box-sizing: border-box;
        }

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 16px;
        }

        .isi-surat p {
            text-align: justify;
            margin: 6px 0;
            line-height: 1.4;
        }

        .data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 10px 0;
        }

        .data-pasien td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .tanda-tangan {
            width: 100%;
            margin-top: 30px;
            text-align: right;
        }

        .ttd-box {
            display: inline-block;
            text-align: left;
            width: 7cm;
        }

        /* Untuk pastikan tidak ada halaman kosong */
        .no-page-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="surat-container">
        <div class="judul-surat" style="margin-top:6cm;">SURAT KETERANGAN KESEHATAN JIWA</div>
        <div class="nomor-surat">NOMOR: {{ $surat_kejiwaan->nomor_surat ?? '____________________' }}</div>

        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini:</p>

            <table class="data-pasien">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><b>{{ $dokter->nama ?? '-' }}</b></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $dokter->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $dokter->jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Instansi</td>
                    <td>:</td>
                    <td>{{ $dokter->instansi ?? '-' }}</td>
                </tr>
            </table>

            <p>
                Telah melakukan pemeriksaan Psikiatrik pada tanggal
                {{ \Carbon\Carbon::parse($surat_kejiwaan->tanggal_pemeriksaan ?? now())->translatedFormat('d F Y') }}
                kepada:
            </p>

            <table class="data-pasien">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><b>{{ $pengajuan_surat->nama ?? '-' }}</b></td>
                </tr>
                <tr>
                    <td>Tempat / Tanggal Lahir</td>
                    <td>:</td>
                    <td>
                        {{ $pengajuan_surat->tempat_lahir ?? '-' }},
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

            <p>
                Hasil pemeriksaan Psikiatrik pada saat ini tidak ditemukan tanda/gejala kejiwaan yang nyata
                dan dinyatakan <strong>{{ strtoupper($surat_kejiwaan->hasil ?? 'SEHAT JIWA') }}</strong>.
            </p>

            <p>
                Demikianlah Surat Keterangan Kesehatan Jiwa ini diperbuat dengan sebenarnya,
                untuk keperluan <strong>{{ $pengajuan_surat->keperluan ?? '_________________' }}</strong>.
            </p>
        </div>

        <div class="tanda-tangan">
            <div class="ttd-box">
                <p>Gunung Lagan,
                    {{ \Carbon\Carbon::parse($surat_kejiwaan->tanggal_pemeriksaan ?? now())->translatedFormat('d F Y') }}
                </p>
                <p>a.n Direktur RSUD Aceh Singkil</p>
                <p style="font-weight: bold;">Dokter yang memeriksa</p>
                <img src="{{ public_path('/assets/plugin-admin/images/QR_kir.png') }}" alt="" width="100px" height="100px">
                <p style="text-decoration: underline; font-weight: bold;">{{ $dokter->nama ?? 'Nama Dokter' }}</p>
                <p>NIP. {{ $dokter->nip ?? 'NIP Dokter' }}</p>
            </div>
        </div>
    </div>
</body>

</html>
