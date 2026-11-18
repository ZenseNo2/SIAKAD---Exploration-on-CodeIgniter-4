<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    protected $table         = 'dosen';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['nidn', 'nama', 'email', 'prodi_id'];

    /** Builder daftar dosen + nama prodi + jumlah kelas di semester aktif. */
    public function daftar(?string $cari = null, ?int $taAktifId = null): self
    {
        $this->select('dosen.*, prodi.nama AS prodi_nama')
            ->select('(SELECT COUNT(*) FROM kelas k WHERE k.dosen_id = dosen.id AND k.tahun_akademik_id = ' . (int) $taAktifId . ') AS jumlah_kelas', false)
            ->join('prodi', 'prodi.id = dosen.prodi_id', 'left')
            ->orderBy('dosen.nama');

        if ($cari) {
            $this->groupStart()->like('dosen.nama', $cari)->orLike('dosen.nidn', $cari)->groupEnd();
        }

        return $this;
    }

    public function options(): array
    {
        return array_column($this->orderBy('nama')->findAll(), 'nama', 'id');
    }
}
