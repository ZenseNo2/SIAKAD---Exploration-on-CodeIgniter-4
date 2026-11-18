<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Libraries\IndeksPrestasi;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\TahunAkademikModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Dosen wali dapat memantau studi mahasiswa bimbingannya.
 */
class Perwalian extends BaseController
{
    public function index()
    {
        $ta   = (new TahunAkademikModel())->aktif();
        $ip   = new IndeksPrestasi();
        $krs  = new KrsModel();
        $rows = (new MahasiswaModel())->daftar()->where('mahasiswa.dosen_wali_id', $this->user['dosen_id'])->findAll();

        foreach ($rows as &$m) {
            $ipk = $ip->ipk((int) $m['id']);
            $m['ipk']       = $ipk['ip'];
            $m['sks_lulus'] = $ipk['sks_lulus'];
            $m['sks_krs']   = $ta ? array_sum(array_column($krs->milikMahasiswa((int) $m['id'], (int) $ta['id']), 'sks')) : 0;
        }
        unset($m);

        return view('dosen/perwalian/index', ['title' => 'Perwalian', 'rows' => $rows, 'ta' => $ta]);
    }

    public function show(int $id)
    {
        $mhs = (new MahasiswaModel())->profil($id);
        if (! $mhs || (int) $mhs['dosen_wali_id'] !== $this->user['dosen_id']) {
            throw new PageNotFoundException('Mahasiswa bukan bimbingan Anda.');
        }
        $ip = new IndeksPrestasi();

        return view('dosen/perwalian/show', [
            'title'    => $mhs['nama'],
            'mhs'      => $mhs,
            'semester' => array_reverse($ip->perSemester($id)),
            'ipk'      => $ip->ipk($id),
        ]);
    }
}
