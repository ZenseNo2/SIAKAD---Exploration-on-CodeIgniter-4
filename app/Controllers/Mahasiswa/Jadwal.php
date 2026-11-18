<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KrsModel;
use App\Models\TahunAkademikModel;

/**
 * Jadwal kuliah mingguan dari KRS semester aktif, dikelompokkan per hari.
 */
class Jadwal extends BaseController
{
    public function index()
    {
        $ta    = (new TahunAkademikModel())->aktif();
        $krs   = $ta ? (new KrsModel())->milikMahasiswa($this->user['mahasiswa_id'], (int) $ta['id']) : [];
        $hari  = [];
        foreach (config('Akademik')->hari as $no => $nama) {
            $hari[$no] = ['nama' => $nama, 'kelas' => []];
        }
        foreach ($krs as $k) {
            $hari[(int) $k['hari']]['kelas'][] = $k;
        }

        return view('mahasiswa/jadwal', [
            'title'   => 'Jadwal Kuliah',
            'ta'      => $ta,
            'hari'    => $hari,
            'hariIni' => (int) date('N'),
        ]);
    }
}
