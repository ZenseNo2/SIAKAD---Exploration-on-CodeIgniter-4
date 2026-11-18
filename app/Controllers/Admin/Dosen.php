<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\ProdiModel;
use App\Models\TahunAkademikModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Data dosen. Setiap dosen otomatis punya akun login
 * (username = NIDN, password awal = NIDN).
 */
class Dosen extends BaseController
{
    private DosenModel $dosen;

    public function __construct()
    {
        $this->dosen = new DosenModel();
    }

    public function index()
    {
        $cari = trim((string) $this->request->getGet('q'));
        $ta   = (new TahunAkademikModel())->aktif();

        return view('admin/dosen/index', [
            'title' => 'Dosen',
            'rows'  => $this->dosen->daftar($cari, $ta['id'] ?? null)->paginate(10),
            'pager' => $this->dosen->pager,
            'cari'  => $cari,
        ]);
    }

    public function new()
    {
        return view('admin/dosen/form', ['title' => 'Dosen Baru', 'row' => null, 'prodi' => (new ProdiModel())->options()]);
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();

        $db = db_connect();
        $db->transStart();
        $id = $this->dosen->insert($data);
        (new UserModel())->buatAkun($data['nidn'], $data['nama'], 'dosen', dosenId: (int) $id);
        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->kembaliDenganError('Gagal menyimpan data dosen.');
        }

        return redirect()->to('/admin/dosen')
            ->with('success', "{$data['nama']} ditambahkan. Akun login: {$data['nidn']} / {$data['nidn']}.");
    }

    public function edit(int $id)
    {
        $row = $this->dosen->find($id) ?? throw new PageNotFoundException();

        return view('admin/dosen/form', ['title' => 'Ubah Dosen', 'row' => $row, 'prodi' => (new ProdiModel())->options()]);
    }

    public function update(int $id)
    {
        $this->dosen->find($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules($id))) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();

        $db = db_connect();
        $db->transStart();
        $this->dosen->update($id, $data);
        (new UserModel())->where('dosen_id', $id)->set(['username' => $data['nidn'], 'nama' => $data['nama']])->update();
        $db->transComplete();

        return redirect()->to('/admin/dosen')->with('success', 'Data dosen diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->dosen->find($id) ?? throw new PageNotFoundException();

        if (db_connect()->table('kelas')->where('dosen_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', "{$row['nama']} masih tercatat mengajar kelas, sehingga tidak dapat dihapus.");
        }

        $db = db_connect();
        $db->transStart();
        $db->table('mahasiswa')->where('dosen_wali_id', $id)->update(['dosen_wali_id' => null]);
        $db->table('users')->where('dosen_id', $id)->delete();
        $this->dosen->delete($id);
        $db->transComplete();

        return redirect()->to('/admin/dosen')->with('success', "{$row['nama']} dihapus.");
    }

    public function resetPassword(int $id)
    {
        $row = $this->dosen->find($id) ?? throw new PageNotFoundException();
        (new UserModel())->where('dosen_id', $id)
            ->set(['password_hash' => password_hash($row['nidn'], PASSWORD_DEFAULT)])->update();

        return redirect()->back()->with('success', "Password {$row['nama']} dikembalikan menjadi NIDN.");
    }

    private function rules(?int $id = null): array
    {
        $userId = $id ? ((new UserModel())->where('dosen_id', $id)->first()['id'] ?? 0) : 0;
        $id ??= 0;

        return [
            'nidn'     => ['label' => 'NIDN', 'rules' => "required|numeric|min_length[8]|max_length[20]|is_unique[dosen.nidn,id,{$id}]|is_unique[users.username,id,{$userId}]"],
            'nama'     => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'email'    => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[100]'],
            'prodi_id' => ['label' => 'Program studi', 'rules' => 'permit_empty|is_not_unique[prodi.id]'],
        ];
    }

    private function input(): array
    {
        $p = $this->request->getPost();

        return [
            'nidn'     => trim($p['nidn']),
            'nama'     => trim($p['nama']),
            'email'    => trim($p['email'] ?? '') ?: null,
            'prodi_id' => ($p['prodi_id'] ?? '') !== '' ? (int) $p['prodi_id'] : null,
        ];
    }
}
