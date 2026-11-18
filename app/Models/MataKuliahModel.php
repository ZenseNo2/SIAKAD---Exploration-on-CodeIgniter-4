<?php

namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table         = 'mata_kuliah';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama', 'sks', 'semester', 'prodi_id'];

    public function daftar(?string $cari = null, ?int $prodiId = null): self
    {
        $this->select('mata_kuliah.*, prodi.nama AS prodi_nama')
            ->join('prodi', 'prodi.id = mata_kuliah.prodi_id')
            ->orderBy('mata_kuliah.prodi_id')->orderBy('mata_kuliah.semester')->orderBy('mata_kuliah.kode');

        if ($cari) {
            $this->groupStart()->like('mata_kuliah.nama', $cari)->orLike('mata_kuliah.kode', $cari)->groupEnd();
        }
        if ($prodiId) {
            $this->where('mata_kuliah.prodi_id', $prodiId);
        }

        return $this;
    }

    /** [id => "IF301 · Pemrograman Web (3 SKS)"] */
    public function options(): array
    {
        $out = [];
        foreach ($this->orderBy('kode')->findAll() as $r) {
            $out[$r['id']] = "{$r['kode']} · {$r['nama']} ({$r['sks']} SKS)";
        }

        return $out;
    }
}
