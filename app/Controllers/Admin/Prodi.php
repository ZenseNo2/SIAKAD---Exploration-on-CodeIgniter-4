<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdiModel;

class Prodi extends BaseController
{
    private ProdiModel $prodi;

    public function __construct()
    {
        $this->prodi = new ProdiModel();
    }

    public function index()
    {
        $rows = $this->prodi
            ->select('prodi.*')
            ->select('(SELECT COUNT(*) FROM mahasiswa WHERE mahasiswa.prodi_id = prodi.id) AS jumlah_mahasiswa', false)
            ->select('(SELECT COUNT(*) FROM mata_kuliah WHERE mata_kuliah.prodi_id = prodi.id) AS jumlah_mk', false)
            ->orderBy('kode')->findAll();

        return view('admin/prodi/index', ['title' => 'Program Studi', 'rows' => $rows]);
    }

    public function new()
    {
        return view('admin/prodi/form', ['title' => 'Program Studi Baru', 'row' => null]);
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $this->prodi->insert($this->input());

        return redirect()->to('/admin/prodi')->with('success', 'Program studi ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = $this->prodi->find($id) ?? throw new \CodeIgniter\Exceptions\PageNotFoundException();

        return view('admin/prodi/form', ['title' => 'Ubah Program Studi', 'row' => $row]);
    }

    public function update(int $id)
    {
        if (! $this->validate($this->rules($id))) {
            return $this->kembaliDenganError();
        }
        $this->prodi->update($id, $this->input());

        return redirect()->to('/admin/prodi')->with('success', 'Program studi diperbarui.');
    }

    public function delete(int $id)
    {
        $db = db_connect();
        $dipakai = $db->table('mahasiswa')->where('prodi_id', $id)->countAllResults()
                 + $db->table('mata_kuliah')->where('prodi_id', $id)->countAllResults();
        if ($dipakai > 0) {
            return redirect()->back()->with('error', 'Program studi masih memiliki mahasiswa atau mata kuliah.');
        }
        $this->prodi->delete($id);

        return redirect()->to('/admin/prodi')->with('success', 'Program studi dihapus.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'kode'    => ['label' => 'Kode', 'rules' => 'required|alpha_numeric|max_length[10]|is_unique[prodi.kode,id,' . ($id ?? 0) . ']'],
            'nama'    => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'jenjang' => ['label' => 'Jenjang', 'rules' => 'required|in_list[D3,D4,S1,S2,S3]'],
        ];
    }

    private function input(): array
    {
        return [
            'kode'    => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'    => trim((string) $this->request->getPost('nama')),
            'jenjang' => $this->request->getPost('jenjang'),
        ];
    }
}
