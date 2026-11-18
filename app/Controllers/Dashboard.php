<?php

namespace App\Controllers;

use App\Libraries\IndeksPrestasi;
use App\Models\DosenModel;
use App\Models\KelasModel;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\TahunAkademikModel;

/**
 * Satu URL /dashboard, isinya berbeda sesuai peran pengguna.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $ta = (new TahunAkademikModel())->aktif();

        return match ($this->user['role']) {
            'admin'     => $this->admin($ta),
            'dosen'     => $this->dosen($ta),
            'mahasiswa' => $this->mahasiswa($ta),
        };
    }

    private function admin(?array $ta)
    {
        $db = db_connect();
        $kelas = $ta ? (new KelasModel())->byTahun((int) $ta['id']) : [];

        // Mahasiswa yang belum mengisi KRS sama sekali di semester aktif
        $belumKrs = 0;
        if ($ta) {
            $belumKrs = $db->table('mahasiswa m')
                ->whereNotIn('m.id', static function ($b) use ($ta) {
                    $b->select('krs.mahasiswa_id')->from('krs')
                        ->join('kelas', 'kelas.id = krs.kelas_id')
                        ->where('kelas.tahun_akademik_id', $ta['id']);
                })
                ->countAllResults();
        }

        return view('dashboard/admin', [
            'title'    => 'Ringkasan',
            'ta'       => $ta,
            'jumlah'   => [
                'mahasiswa'   => (new MahasiswaModel())->countAllResults(),
                'dosen'       => (new DosenModel())->countAllResults(),
                'mata_kuliah' => (new MataKuliahModel())->countAllResults(),
                'kelas'       => count($kelas),
            ],
            'belumKrs' => $belumKrs,
            'kelas'    => $kelas,
        ]);
    }

    private function dosen(?array $ta)
    {
        $dosenId = $this->user['dosen_id'];
        $kelas   = $ta ? (new KelasModel())->byDosen($dosenId, (int) $ta['id']) : [];
        $hariIni = (int) date('N');

        $wali = (new MahasiswaModel())->where('dosen_wali_id', $dosenId)->orderBy('nim')->findAll();
        $ip   = new IndeksPrestasi();
        foreach ($wali as &$m) {
            $m['ipk'] = $ip->ipk((int) $m['id'])['ip'];
        }
        unset($m);

        return view('dashboard/dosen', [
            'title'   => 'Ringkasan',
            'ta'      => $ta,
            'dosen'   => (new DosenModel())->find($dosenId),
            'kelas'   => $kelas,
            'jadwal'  => array_values(array_filter($kelas, static fn ($k) => (int) $k['hari'] === $hariIni)),
            'wali'    => $wali,
        ]);
    }

    private function mahasiswa(?array $ta)
    {
        $mhsId = $this->user['mahasiswa_id'];
        $ip    = new IndeksPrestasi();
        $krs   = $ta ? (new KrsModel())->milikMahasiswa($mhsId, (int) $ta['id']) : [];
        $semester = $ip->perSemester($mhsId);
        $terakhirDinilai = null;
        foreach ($semester as $s) {
            if ($s['ips'] !== null) {
                $terakhirDinilai = $s;
            }
        }

        return view('dashboard/mahasiswa', [
            'title'     => 'Ringkasan',
            'ta'        => $ta,
            'mhs'       => (new MahasiswaModel())->profil($mhsId),
            'ipk'       => $ip->ipk($mhsId),
            'terakhir'  => $terakhirDinilai,
            'semester'  => $semester,
            'krs'       => $krs,
            'sksKrs'    => array_sum(array_column($krs, 'sks')),
            'batas'     => $ta ? $ip->batasSks($mhsId, (int) $ta['id']) : null,
            'jadwal'    => array_values(array_filter($krs, static fn ($k) => (int) $k['hari'] === (int) date('N'))),
        ]);
    }
}
