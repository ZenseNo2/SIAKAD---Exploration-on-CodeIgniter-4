<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Libraries\IndeksPrestasi;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;

/**
 * Transkrip: seluruh mata kuliah yang sudah dinilai (nilai terbaik bila mengulang) dan IPK.
 */
class Transkrip extends BaseController
{
    public function index()
    {
        $mhsId = $this->user['mahasiswa_id'];
        $rows  = IndeksPrestasi::nilaiTerbaik((new KrsModel())->riwayat($mhsId));

        // Kelompokkan per semester kurikulum mata kuliah
        $grup = [];
        foreach ($rows as $r) {
            $grup[(int) $r['mk_semester']][] = $r;
        }
        ksort($grup);

        return view('mahasiswa/transkrip', [
            'title' => 'Transkrip Nilai',
            'mhs'   => (new MahasiswaModel())->profil($mhsId),
            'grup'  => $grup,
            'ipk'   => IndeksPrestasi::hitung($rows),
        ]);
    }
}
