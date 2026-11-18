<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use App\Models\ProdiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MataKuliah extends BaseController
{
    private MataKuliahModel $mk;

    public function __construct()
    {
        $this->mk = new MataKuliahModel();
    }

    public function index()
    {
        $cari    = trim((string) $this->request->getGet('q'));
        $prodiId = (int) $this->request->getGet('prodi') ?: null;

        return view('admin/mata_kuliah/index', [
            'title'   => 'Mata Kuliah',
            'rows'    => $this->mk->daftar($cari, $prodiId)->paginate(15),
            'pager'   => $this->mk->pager,
            'cari'    => $cari,
            'prodiId' => $prodiId,
            'prodi'   => (new ProdiModel())->options(),
        ]);
    }

    public function new()
    {
        return view('admin/mata_kuliah/form', ['title' => 'Mata Kuliah Baru', 'row' => null, 'prodi' => (new ProdiModel())->options()]);
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $this->mk->insert($this->input());

        return redirect()->to('/admin/mata-kuliah')->with('success', 'Mata kuliah ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = $this->mk->find($id) ?? throw new PageNotFoundException();

        return view('admin/mata_kuliah/form', ['title' => 'Ubah Mata Kuliah', 'row' => $row, 'prodi' => (new ProdiModel())->options()]);
    }

    public function update(int $id)
    {
        $this->mk->find($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules($id))) {
            return $this->kembaliDenganError();
        }
        $this->mk->update($id, $this->input());

        return redirect()->to('/admin/mata-kuliah')->with('success', 'Mata kuliah diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->mk->find($id) ?? throw new PageNotFoundException();
        if (db_connect()->table('kelas')->where('mata_kuliah_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', "{$row['nama']} sudah dipakai di jadwal kelas, sehingga tidak dapat dihapus.");
        }
        $this->mk->delete($id);

        return redirect()->to('/admin/mata-kuliah')->with('success', "{$row['nama']} dihapus.");
    }

    private function rules(?int $id = null): array
    {
        return [
            'kode'     => ['label' => 'Kode', 'rules' => 'required|alpha_numeric|max_length[15]|is_unique[mata_kuliah.kode,id,' . ($id ?? 0) . ']'],
            'nama'     => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'sks'      => ['label' => 'SKS', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[6]'],
            'semester' => ['label' => 'Semester', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[8]'],
            'prodi_id' => ['label' => 'Program studi', 'rules' => 'required|is_not_unique[prodi.id]'],
        ];
    }

    private function input(): array
    {
        $p = $this->request->getPost();

        return [
            'kode'     => strtoupper(trim($p['kode'])),
            'nama'     => trim($p['nama']),
            'sks'      => (int) $p['sks'],
            'semester' => (int) $p['semester'],
            'prodi_id' => (int) $p['prodi_id'],
        ];
    }
}
