<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Libraries\IndeksPrestasi;
use App\Models\MahasiswaModel;

/**
 * Kartu Hasil Studi: nilai dan IPS per semester.
 */
class Khs extends BaseController
{
    public function index()
    {
        $mhsId    = $this->user['mahasiswa_id'];
        $semester = (new IndeksPrestasi())->perSemester($mhsId);

        // Semester yang dipilih (?ta=), default: semester terbaru
        $dipilih = end($semester) ?: null;
        $taId    = (int) $this->request->getGet('ta');
        foreach ($semester as $s) {
            if ($s['ta_id'] === $taId) {
                $dipilih = $s;
            }
        }

        return view('mahasiswa/khs', [
            'title'    => 'Kartu Hasil Studi',
            'mhs'      => (new MahasiswaModel())->profil($mhsId),
            'semester' => array_reverse($semester),
            'dipilih'  => $dipilih,
        ]);
    }
}
