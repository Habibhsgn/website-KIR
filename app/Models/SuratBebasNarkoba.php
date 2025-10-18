<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratBebasNarkoba extends Model
{
    use HasFactory;

    protected $table = 'surat_bebas_narkoba';

    protected $fillable = [
        'pengajuan_id',
        'dokter_id',
        'fisik_diagnostik',
        'psikiatrik',
        'pemeriksaan_tambahan',
        'penampilan',
        'cara_berjalan',
        'cara_bicara',
        'konjungtiva',
        'bekas_suntikan',
        'tremor',
        'alur_pembicaraan',
        'waham',
        'halusinasi',
        'halusinasi_akustik',
        'halusinasi_visual',
        'halusinasi_lain',
        'cannabis',
        'opiate',
        'metamphetamine',
        'mdma',
        'benzodiazepine',
        'tidak_ada_penyalahgunaan',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSurat::class, 'pengajuan_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}
