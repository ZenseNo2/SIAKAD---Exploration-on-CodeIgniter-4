<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdiModel extends Model
{
    protected $table         = 'prodi';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'nama', 'jenjang'];

    /** [id => "S1 Informatika"] untuk dropdown */
    public function options(): array
    {
        $rows = $this->orderBy('nama')->findAll();

        return array_column(array_map(static fn ($r) => ['id' => $r['id'], 'label' => $r['jenjang'] . ' ' . $r['nama']], $rows), 'label', 'id');
    }
}
