<!DOCTYPE html>
<html>

<head>
    <title>Preview Surat Keterangan Kesehatan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            background-image: url('{{ asset('assets/img/template_surat.png') }}');
            background-size: contain;
            /* biar fit ke A4, tidak menutupi teks */
            background-position: top center;
            background-repeat: no-repeat;
        }

        .surat-container {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 3.5cm 2cm 2cm;
            /* naikkan top padding sedikit biar tidak kena kop */
            box-sizing: border-box;
        }

        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 100px;
            /* geser ke bawah dari kop */
            margin-bottom: 15px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 25px;
        }

        .isi-surat {
            line-height: 1.5;
            /* lebih rapat dari sebelumnya */
            text-align: justify;
        }

        table.data-pasien {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-pasien td {
            padding: 2px 4px;
            /* lebih rapat */
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

        .catatan {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 11pt;
            line-height: 1.3;
        }
    </style>

</head>

<body>
    <div class="surat-container">
        <div class="judul-surat">SURAT KETERANGAN KESEHATAN</div>
        <div class="nomor-surat">NOMOR: {{ $surat_kesehatan->nomor_surat ?? '____________________' }}</div>

        <div class="isi-surat">
            <p>
                Yang bertanda tangan dibawah ini, <strong>{{ $dokter->nama ?? 'Nama Dokter' }}</strong>,
                {{ $dokter->jabatan ?? 'Jabatan' }} pada {{ $dokter->instansi ?? 'Rumah Sakit' }}, menerangkan bahwa:
            </p>

            <table class="data-pasien">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tempat / Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $pengajuan_surat->tempat_lahir ?? '-' }}/{{ \Carbon\Carbon::parse($pengajuan_surat->tanggal_lahir ?? now())->format('d F Y') }}
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
                <strong>{{ $surat_kesehatan->isi_keterangan ?? 'Sehat Jasmani dan Rohani' }}</strong>, untuk:
            </p>

            <p style="text-align: center; font-weight: bold; margin: 30px 0;">
                {{ $pengajuan_surat->keperluan ?? '____________________________' }}
            </p>

            <p>
                Demikianlah Surat Keterangan Kesehatan ini dibuat dengan sebenarnya agar dapat dipergunakan seperlunya.
            </p>
        </div>

        <div class="tanda-tangan">
            <div class="ttd-box">
                <p>Gunung Lagan,
                    {{ \Carbon\Carbon::parse($surat_kesehatan->tanggal_pemeriksaan ?? now())->format('d F Y') }}</p>
                <p>a.n Direktur RSUD Aceh Singkil</p>
                <p style="font-weight: bold;">Dokter yang memeriksa</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">{{ $dokter->nama ?? 'Nama Dokter' }}</p>
                <p>NIP. {{ $dokter->nip ?? 'NIP Dokter' }}</p>
            </div>
        </div>

        <div class="catatan">
            <p style="font-weight: bold;">Catatan:</p>
            <ul>
                <li>Tinggi Badan: {{ $surat_kesehatan->tinggi_badan ?? '___' }} cm</li>
                <li>Berat Badan: {{ $surat_kesehatan->berat_badan ?? '___' }} kg</li>
                <li>Tensi Darah: {{ $surat_kesehatan->tensi_darah ?? '___' }} mmHg</li>
                <li>Golongan Darah: {{ $surat_kesehatan->golongan_darah ?? '___' }}</li>
            </ul>
        </div>
    </div>
</body>

</html>
