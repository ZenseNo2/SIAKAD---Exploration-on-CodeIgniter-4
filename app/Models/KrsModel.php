<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KRS (Kartu Rencana Studi). Satu baris = satu mahasiswa di satu kelas.
 * Nilai tugas/UTS/UAS dan nilai akhir disimpan pada baris yang sama.
 */
class KrsModel extends Model
{
    protected $table         = 'krs';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'mahasiswa_id', 'kelas_id',
        'nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_akhir', 'nilai_huruf',
    ];

    /** KRS + data kelas, mata kuliah, dosen, tahun akademik. */
    private function lengkap(): self
    {
        return $this->select('krs.*, kelas.nama_kelas, kelas.hari, kelas.jam_mulai, kelas.jam_selesai, kelas.ruang')
            ->select('kelas.tahun_akademik_id, mk.id AS mata_kuliah_id, mk.kode, mk.nama AS mk_nama, mk.sks, mk.semester AS mk_semester')
            ->select('dosen.nama AS dosen_nama, ta.tahun, ta.semester AS ta_semester')
            ->join('kelas', 'kelas.id = krs.kelas_id')
            ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
            ->join('dosen', 'dosen.id = kelas.dosen_id')
            ->join('tahun_akademik ta', 'ta.id = kelas.tahun_akademik_id');
    }

    /** KRS seorang mahasiswa pada satu tahun akademik. */
    public function milikMahasiswa(int $mahasiswaId, int $taId): array
    {
        return $this->lengkap()
            ->where('krs.mahasiswa_id', $mahasiswaId)
            ->where('kelas.tahun_akademik_id', $taId)
            ->orderBy('kelas.hari')->orderBy('kelas.jam_mulai')
            ->findAll();
    }

    /** Seluruh riwayat KRS mahasiswa (untuk KHS & transkrip). */
    public function riwayat(int $mahasiswaId): array
    {
        return $this->lengkap()
            ->where('krs.mahasiswa_id', $mahasiswaId)
            ->orderBy('ta.tahun')->orderBy('ta.semester')->orderBy('mk.kode')
            ->findAll();
    }

    /** Peserta sebuah kelas beserta nilainya (untuk dosen). */
    public function peserta(int $kelasId): array
    {
        return $this->select('krs.*, m.nim, m.nama, m.angkatan')
            ->join('mahasiswa m', 'm.id = krs.mahasiswa_id')
            ->where('krs.kelas_id', $kelasId)
            ->orderBy('m.nim')
            ->findAll();
    }

    /** Tahun akademik yang pernah diikuti mahasiswa, terbaru lebih dulu. */
    public function semesterDiikuti(int $mahasiswaId): array
    {
        return $this->db->table('krs')
            ->select('ta.id, ta.tahun, ta.semester')
            ->distinct()
            ->join('kelas', 'kelas.id = krs.kelas_id')
            ->join('tahun_akademik ta', 'ta.id = kelas.tahun_akademik_id')
            ->where('krs.mahasiswa_id', $mahasiswaId)
            ->orderBy('ta.tahun', 'DESC')->orderBy('ta.semester', 'DESC')
            ->get()->getResultArray();
    }
}
