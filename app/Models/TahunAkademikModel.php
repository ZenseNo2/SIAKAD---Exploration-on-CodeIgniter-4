<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAkademikModel extends Model
{
    protected $table         = 'tahun_akademik';
    protected $returnType    = 'array';
    protected $allowedFields = ['tahun', 'semester', 'is_active', 'krs_dibuka'];

    /** Tahun akademik yang sedang aktif (hanya boleh satu). */
    public function aktif(): ?array
    {
        return $this->where('is_active', 1)->first();
    }

    public function terbaru(): array
    {
        // "Genap" > "Ganjil" secara alfabet, sehingga DESC = semester terbaru di atas
        return $this->orderBy('tahun', 'DESC')->orderBy('semester', 'DESC')->findAll();
    }

    /** Menjadikan satu tahun akademik aktif dan menonaktifkan yang lain. */
    public function aktifkan(int $id): void
    {
        $this->db->transStart();
        $this->builder()->set(['is_active' => 0, 'krs_dibuka' => 0])->update();
        $this->update($id, ['is_active' => 1]);
        $this->db->transComplete();
    }

    public static function label(?array $ta): string
    {
        return $ta ? "{$ta['tahun']} {$ta['semester']}" : 'Belum diatur';
    }
}
