<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAkademikModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Tahun akademik menentukan "semester yang sedang berjalan".
 * Hanya satu yang aktif; KRS hanya bisa diisi bila krs_dibuka = 1.
 */
class TahunAkademik extends BaseController
{
    private TahunAkademikModel $ta;

    public function __construct()
    {
        $this->ta = new TahunAkademikModel();
    }

    public function index()
    {
        $rows = $this->ta->select('tahun_akademik.*')
            ->select('(SELECT COUNT(*) FROM kelas WHERE kelas.tahun_akademik_id = tahun_akademik.id) AS jumlah_kelas', false)
            ->orderBy('tahun', 'DESC')->orderBy('semester', 'DESC')->findAll();

        return view('admin/tahun_akademik/index', ['title' => 'Tahun Akademik', 'rows' => $rows]);
    }

    public function create()
    {
        $rules = [
            'tahun'    => ['label' => 'Tahun', 'rules' => 'required|regex_match[/^\d{4}\/\d{4}$/]'],
            'semester' => ['label' => 'Semester', 'rules' => 'required|in_list[Ganjil,Genap]'],
        ];
        if (! $this->validate($rules)) {
            return $this->kembaliDenganError();
        }

        $tahun = $this->request->getPost('tahun');
        [$awal, $akhir] = array_map('intval', explode('/', $tahun));
        if ($akhir !== $awal + 1) {
            return $this->kembaliDenganError('Format tahun harus berurutan, contoh 2026/2027.');
        }
        if ($this->ta->where(['tahun' => $tahun, 'semester' => $this->request->getPost('semester')])->countAllResults() > 0) {
            return $this->kembaliDenganError('Tahun akademik tersebut sudah ada.');
        }

        $this->ta->insert(['tahun' => $tahun, 'semester' => $this->request->getPost('semester'), 'is_active' => 0, 'krs_dibuka' => 0]);

        return redirect()->to('/admin/tahun-akademik')->with('success', "Tahun akademik {$tahun} {$this->request->getPost('semester')} ditambahkan.");
    }

    public function aktifkan(int $id)
    {
        $row = $this->ta->find($id) ?? throw new PageNotFoundException();
        $this->ta->aktifkan($id);

        return redirect()->back()->with('success', TahunAkademikModel::label($row) . ' sekarang menjadi semester aktif.');
    }

    public function toggleKrs(int $id)
    {
        $row = $this->ta->find($id) ?? throw new PageNotFoundException();
        if (! $row['is_active']) {
            return redirect()->back()->with('error', 'KRS hanya bisa dibuka pada tahun akademik yang aktif.');
        }
        $buka = $row['krs_dibuka'] ? 0 : 1;
        $this->ta->update($id, ['krs_dibuka' => $buka]);

        return redirect()->back()->with('success', $buka ? 'Periode KRS dibuka.' : 'Periode KRS ditutup.');
    }

    public function delete(int $id)
    {
        $row = $this->ta->find($id) ?? throw new PageNotFoundException();
        if ($row['is_active']) {
            return redirect()->back()->with('error', 'Tahun akademik yang aktif tidak dapat dihapus.');
        }
        if (db_connect()->table('kelas')->where('tahun_akademik_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Tahun akademik ini sudah memiliki kelas.');
        }
        $this->ta->delete($id);

        return redirect()->to('/admin/tahun-akademik')->with('success', 'Tahun akademik dihapus.');
    }
}
