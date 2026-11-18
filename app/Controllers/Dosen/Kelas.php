<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\KrsModel;
use App\Models\TahunAkademikModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Kelas yang diajar dosen dan input nilai peserta.
 */
class Kelas extends BaseController
{
    public function index()
    {
        $kelas = (new KelasModel())->byDosen($this->user['dosen_id']);
        $ta    = (new TahunAkademikModel())->aktif();

        // Kelompokkan per tahun akademik untuk ditampilkan per section
        $grup = [];
        foreach ($kelas as $k) {
            $grup["{$k['tahun']} {$k['ta_semester']}"][] = $k;
        }

        return view('dosen/kelas/index', ['title' => 'Kelas Saya', 'grup' => $grup, 'ta' => $ta]);
    }

    public function nilai(int $id)
    {
        $kelas = $this->kelasMilikSaya($id);

        return view('dosen/kelas/nilai', [
            'title'   => "{$kelas['kode']} {$kelas['mk_nama']}",
            'kelas'   => $kelas,
            'peserta' => (new KrsModel())->peserta($id),
            'bobot'   => config('Akademik')->bobot,
            'skala'   => config('Akademik')->skala,
        ]);
    }

    /**
     * Menyimpan nilai semua peserta sekaligus.
     * Form mengirim array: nilai[krs_id][tugas|uts|uas]
     */
    public function simpanNilai(int $id)
    {
        $this->kelasMilikSaya($id);
        $krsModel = new KrsModel();
        $input    = (array) $this->request->getPost('nilai');

        // ID KRS yang sah untuk kelas ini (mencegah manipulasi form untuk kelas lain)
        $sah = array_column($krsModel->where('kelas_id', $id)->findAll(), 'id', 'id');

        $salah = [];
        $tersimpan = 0;
        $db = db_connect();
        $db->transStart();

        foreach ($input as $krsId => $n) {
            if (! isset($sah[$krsId])) {
                continue;
            }
            $nilai = [];
            foreach (['tugas', 'uts', 'uas'] as $komponen) {
                $v = trim(str_replace(',', '.', (string) ($n[$komponen] ?? '')));
                if ($v === '') {
                    $nilai[$komponen] = null;
                } elseif (is_numeric($v) && $v >= 0 && $v <= 100) {
                    $nilai[$komponen] = (float) $v;
                } else {
                    $salah[] = $krsId;
                    continue 2; // lewati peserta ini, nilainya tidak valid
                }
            }

            $akhir = hitung_nilai_akhir($nilai['tugas'], $nilai['uts'], $nilai['uas']);
            $krsModel->update($krsId, [
                'nilai_tugas' => $nilai['tugas'],
                'nilai_uts'   => $nilai['uts'],
                'nilai_uas'   => $nilai['uas'],
                'nilai_akhir' => $akhir,
                'nilai_huruf' => nilai_huruf($akhir),
            ]);
            $tersimpan++;
        }
        $db->transComplete();

        $redirect = redirect()->to("/dosen/kelas/{$id}")->with('success', "Nilai {$tersimpan} mahasiswa disimpan.");
        if ($salah) {
            $redirect->with('error', count($salah) . ' baris dilewati karena nilainya bukan angka 0–100.');
        }

        return $redirect;
    }

    /** Pastikan kelas ada DAN diajar oleh dosen yang sedang login. */
    private function kelasMilikSaya(int $id): array
    {
        $kelas = (new KelasModel())->findDetail($id);
        if (! $kelas || (int) $kelas['dosen_id'] !== $this->user['dosen_id']) {
            throw new PageNotFoundException('Kelas tidak ditemukan.');
        }

        return $kelas;
    }
}
