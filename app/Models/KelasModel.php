<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Kelas = jadwal kuliah: satu mata kuliah, satu dosen, pada satu tahun akademik.
 */
class KelasModel extends Model
{
    protected $table         = 'kelas';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'tahun_akademik_id', 'mata_kuliah_id', 'dosen_id', 'nama_kelas',
        'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota',
    ];

    /** Kolom gabungan yang hampir selalu dibutuhkan saat menampilkan kelas. */
    public function detail(): self
    {
        return $this->select('kelas.*, mk.kode, mk.nama AS mk_nama, mk.sks, mk.semester AS mk_semester, mk.prodi_id')
            ->select('dosen.nama AS dosen_nama, ta.tahun, ta.semester AS ta_semester')
            ->select('(SELECT COUNT(*) FROM krs WHERE krs.kelas_id = kelas.id) AS jumlah_peserta', false)
            ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
            ->join('dosen', 'dosen.id = kelas.dosen_id')
            ->join('tahun_akademik ta', 'ta.id = kelas.tahun_akademik_id');
    }

    public function byTahun(int $taId, ?int $prodiId = null): array
    {
        $this->detail()->where('kelas.tahun_akademik_id', $taId);
        if ($prodiId) {
            $this->where('mk.prodi_id', $prodiId);
        }

        return $this->orderBy('kelas.hari')->orderBy('kelas.jam_mulai')->orderBy('mk.kode')->findAll();
    }

    public function byDosen(int $dosenId, ?int $taId = null): array
    {
        $this->detail()->where('kelas.dosen_id', $dosenId);
        if ($taId) {
            $this->where('kelas.tahun_akademik_id', $taId);
        }

        return $this->orderBy('ta.tahun', 'DESC')->orderBy('ta.semester', 'DESC')
            ->orderBy('kelas.hari')->orderBy('kelas.jam_mulai')->findAll();
    }

    public function findDetail(int $id): ?array
    {
        return $this->detail()->where('kelas.id', $id)->first();
    }

    /**
     * Mencari kelas lain di tahun akademik yang sama yang jadwalnya bentrok
     * dengan $data, dilihat dari dosen yang sama ATAU ruang yang sama.
     */
    public function cariBentrok(array $data, ?int $kecualiId = null): ?array
    {
        $this->detail()
            ->where('kelas.tahun_akademik_id', $data['tahun_akademik_id'])
            ->where('kelas.hari', $data['hari'])
            ->where('kelas.jam_mulai <', $data['jam_selesai'])
            ->where('kelas.jam_selesai >', $data['jam_mulai'])
            ->groupStart()
            ->where('kelas.dosen_id', $data['dosen_id'])
            ->orWhere('kelas.ruang', $data['ruang'])
            ->groupEnd();

        if ($kecualiId) {
            $this->where('kelas.id !=', $kecualiId);
        }

        return $this->first();
    }
}
