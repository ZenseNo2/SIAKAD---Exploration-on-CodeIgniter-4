<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Data mahasiswa. Setiap mahasiswa otomatis punya akun login
 * (username = NIM, password awal = NIM).
 */
class Mahasiswa extends BaseController
{
    private MahasiswaModel $mahasiswa;

    public function __construct()
    {
        $this->mahasiswa = new MahasiswaModel();
    }

    public function index()
    {
        $cari    = trim((string) $this->request->getGet('q'));
        $prodiId = (int) $this->request->getGet('prodi') ?: null;

        return view('admin/mahasiswa/index', [
            'title'   => 'Mahasiswa',
            'rows'    => $this->mahasiswa->daftar($cari, $prodiId)->paginate(10),
            'pager'   => $this->mahasiswa->pager,
            'cari'    => $cari,
            'prodiId' => $prodiId,
            'prodi'   => (new ProdiModel())->options(),
        ]);
    }

    public function new()
    {
        return view('admin/mahasiswa/form', $this->dataForm('Mahasiswa Baru', null));
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();

        // Simpan mahasiswa dan akunnya dalam satu transaksi: berhasil semua atau batal semua
        $db = db_connect();
        $db->transStart();
        $id = $this->mahasiswa->insert($data);
        (new UserModel())->buatAkun($data['nim'], $data['nama'], 'mahasiswa', mahasiswaId: (int) $id);
        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->kembaliDenganError('Gagal menyimpan data mahasiswa.');
        }

        return redirect()->to('/admin/mahasiswa')
            ->with('success', "Mahasiswa {$data['nama']} ditambahkan. Akun login: {$data['nim']} / {$data['nim']}.");
    }

    public function edit(int $id)
    {
        $row = $this->mahasiswa->find($id) ?? throw new PageNotFoundException();

        return view('admin/mahasiswa/form', $this->dataForm('Ubah Mahasiswa', $row));
    }

    public function update(int $id)
    {
        $lama = $this->mahasiswa->find($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules($id))) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();

        $db = db_connect();
        $db->transStart();
        $this->mahasiswa->update($id, $data);
        // Username akun mengikuti NIM
        (new UserModel())->where('mahasiswa_id', $id)->set(['username' => $data['nim'], 'nama' => $data['nama']])->update();
        $db->transComplete();

        return redirect()->to('/admin/mahasiswa')->with('success', "Data {$lama['nama']} diperbarui.");
    }

    public function delete(int $id)
    {
        $row = $this->mahasiswa->find($id) ?? throw new PageNotFoundException();

        // Akun dan KRS ikut terhapus
        $db = db_connect();
        $db->transStart();
        $db->table('krs')->where('mahasiswa_id', $id)->delete();
        $db->table('users')->where('mahasiswa_id', $id)->delete();
        $this->mahasiswa->delete($id);
        $db->transComplete();

        return redirect()->to('/admin/mahasiswa')->with('success', "{$row['nama']} dihapus beserta akun dan KRS-nya.");
    }

    public function resetPassword(int $id)
    {
        $row = $this->mahasiswa->find($id) ?? throw new PageNotFoundException();
        (new UserModel())->where('mahasiswa_id', $id)
            ->set(['password_hash' => password_hash($row['nim'], PASSWORD_DEFAULT)])->update();

        return redirect()->back()->with('success', "Password {$row['nama']} dikembalikan menjadi NIM.");
    }

    private function dataForm(string $title, ?array $row): array
    {
        return [
            'title' => $title,
            'row'   => $row,
            'prodi' => (new ProdiModel())->options(),
            'dosen' => (new DosenModel())->options(),
        ];
    }

    private function rules(?int $id = null): array
    {
        // is_unique juga dicek ke tabel users agar NIM tidak bentrok dengan username lain
        $userId = $id ? ((new UserModel())->where('mahasiswa_id', $id)->first()['id'] ?? 0) : 0;
        $id ??= 0;

        return [
            'nim'           => ['label' => 'NIM', 'rules' => "required|alpha_numeric|min_length[5]|max_length[20]|is_unique[mahasiswa.nim,id,{$id}]|is_unique[users.username,id,{$userId}]"],
            'nama'          => ['label' => 'Nama', 'rules' => 'required|max_length[100]'],
            'prodi_id'      => ['label' => 'Program studi', 'rules' => 'required|is_not_unique[prodi.id]'],
            'angkatan'      => ['label' => 'Angkatan', 'rules' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . (date('Y') + 1) . ']'],
            'jenis_kelamin' => ['label' => 'Jenis kelamin', 'rules' => 'required|in_list[L,P]'],
            'email'         => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[100]'],
            'dosen_wali_id' => ['label' => 'Dosen wali', 'rules' => 'permit_empty|is_not_unique[dosen.id]'],
        ];
    }

    private function input(): array
    {
        $p = $this->request->getPost();

        return [
            'nim'           => trim($p['nim']),
            'nama'          => trim($p['nama']),
            'prodi_id'      => (int) $p['prodi_id'],
            'angkatan'      => (int) $p['angkatan'],
            'jenis_kelamin' => $p['jenis_kelamin'],
            'email'         => trim($p['email'] ?? '') ?: null,
            'dosen_wali_id' => ($p['dosen_wali_id'] ?? '') !== '' ? (int) $p['dosen_wali_id'] : null,
        ];
    }
}
