<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table         = 'mahasiswa';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['nim', 'nama', 'prodi_id', 'angkatan', 'jenis_kelamin', 'email', 'dosen_wali_id'];

    /** Builder daftar mahasiswa dengan filter pencarian & prodi (dipakai bersama paginate()). */
    public function daftar(?string $cari = null, ?int $prodiId = null): self
    {
        $this->select('mahasiswa.*, prodi.nama AS prodi_nama, dosen.nama AS wali_nama')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->orderBy('mahasiswa.nim');

        if ($cari) {
            $this->groupStart()->like('mahasiswa.nama', $cari)->orLike('mahasiswa.nim', $cari)->groupEnd();
        }
        if ($prodiId) {
            $this->where('mahasiswa.prodi_id', $prodiId);
        }

        return $this;
    }

    /** Profil lengkap satu mahasiswa. */
    public function profil(int $id): ?array
    {
        return $this->select('mahasiswa.*, prodi.nama AS prodi_nama, prodi.jenjang, dosen.nama AS wali_nama')
            ->join('prodi', 'prodi.id = mahasiswa.prodi_id')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->find($id);
    }
}
