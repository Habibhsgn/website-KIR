<!DOCTYPE html>
<html>
<head>
    <title>Preview Surat Keterangan Kesehatan Jiwa</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            background-image: url('{{ asset('assets/img/template_surat.png') }}');
            background-size: contain;
            background-position: top center;
            background-repeat: no-repeat;
        }

        .surat-container {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 3.5cm 2cm 2cm;
            box-sizing: border-box;
        }

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 100px;
            margin-bottom: 15px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 25px;
        }

        .isi-surat {
            line-height: 1.5;
            text-align: justify;
        }

        table.data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-pasien td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .tanda-tangan {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            margin-right: 50px;
        }

        .ttd-box {
            width: 50%;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="surat-container">
    <div class="judul-surat">SURAT KETERANGAN KESEHATAN JIWA</div>
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
            <p>Gunung Lagan, {{ \Carbon\Carbon::parse($surat_kejiwaan->tanggal_pemeriksaan ?? now())->translatedFormat('d F Y') }}</p>
            <p>a.n Direktur RSUD Aceh Singkil</p>
            <p style="font-weight: bold;">Dokter yang memeriksa</p>
            <br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">{{ $dokter->nama ?? 'Nama Dokter' }}</p>
            <p>NIP. {{ $dokter->nip ?? 'NIP Dokter' }}</p>
        </div>
    </div>
</div>
</body>
</html>
