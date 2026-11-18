<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\KelasModel;
use App\Models\KrsModel;
use App\Models\MataKuliahModel;
use App\Models\ProdiModel;
use App\Models\TahunAkademikModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Jadwal kelas per tahun akademik.
 * Saat disimpan, sistem menolak jadwal yang bentrok (dosen atau ruang yang sama pada jam yang beririsan).
 */
class Kelas extends BaseController
{
    private KelasModel $kelas;
    private TahunAkademikModel $ta;

    public function __construct()
    {
        $this->kelas = new KelasModel();
        $this->ta    = new TahunAkademikModel();
    }

    public function index()
    {
        $taId    = (int) $this->request->getGet('ta') ?: (int) ($this->ta->aktif()['id'] ?? 0);
        $prodiId = (int) $this->request->getGet('prodi') ?: null;

        return view('admin/kelas/index', [
            'title'     => 'Jadwal Kelas',
            'rows'      => $taId ? $this->kelas->byTahun($taId, $prodiId) : [],
            'taId'      => $taId,
            'prodiId'   => $prodiId,
            'daftarTa'  => $this->ta->terbaru(),
            'prodi'     => (new ProdiModel())->options(),
        ]);
    }

    public function show(int $id)
    {
        $kelas = $this->kelas->findDetail($id) ?? throw new PageNotFoundException();

        return view('admin/kelas/show', [
            'title'   => "{$kelas['kode']} {$kelas['mk_nama']}",
            'kelas'   => $kelas,
            'peserta' => (new KrsModel())->peserta($id),
        ]);
    }

    public function new()
    {
        return view('admin/kelas/form', $this->dataForm('Kelas Baru', [
            'tahun_akademik_id' => (int) $this->request->getGet('ta') ?: ($this->ta->aktif()['id'] ?? null),
            'kuota'             => 40,
        ], true));
    }

    public function create()
    {
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();
        if ($pesan = $this->cekJadwal($data)) {
            return $this->kembaliDenganError($pesan);
        }
        $this->kelas->insert($data);

        return redirect()->to('/admin/kelas?ta=' . $data['tahun_akademik_id'])->with('success', 'Kelas ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = $this->kelas->find($id) ?? throw new PageNotFoundException();

        return view('admin/kelas/form', $this->dataForm('Ubah Kelas', $row));
    }

    public function update(int $id)
    {
        $lama = $this->kelas->findDetail($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules())) {
            return $this->kembaliDenganError();
        }
        $data = $this->input();
        if ($data['kuota'] < (int) $lama['jumlah_peserta']) {
            return $this->kembaliDenganError("Kuota tidak boleh kurang dari jumlah peserta saat ini ({$lama['jumlah_peserta']}).");
        }
        if ($pesan = $this->cekJadwal($data, $id)) {
            return $this->kembaliDenganError($pesan);
        }
        $this->kelas->update($id, $data);

        return redirect()->to('/admin/kelas?ta=' . $data['tahun_akademik_id'])->with('success', 'Kelas diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->kelas->findDetail($id) ?? throw new PageNotFoundException();
        if ((int) $row['jumlah_peserta'] > 0) {
            return redirect()->back()->with('error', "Kelas {$row['kode']} {$row['nama_kelas']} sudah memiliki {$row['jumlah_peserta']} peserta.");
        }
        $this->kelas->delete($id);

        return redirect()->back()->with('success', 'Kelas dihapus.');
    }

    /** Validasi lanjutan yang tidak bisa dilakukan oleh aturan validasi standar. */
    private function cekJadwal(array $data, ?int $kecualiId = null): ?string
    {
        if ($data['jam_selesai'] <= $data['jam_mulai']) {
            return 'Jam selesai harus setelah jam mulai.';
        }
        $bentrok = $this->kelas->cariBentrok($data, $kecualiId);
        if ($bentrok) {
            $alasan = (int) $bentrok['dosen_id'] === $data['dosen_id'] ? 'dosen yang sama' : "ruang {$bentrok['ruang']}";

            return sprintf(
                'Jadwal bentrok dengan %s %s (%s, %s–%s) karena %s.',
                $bentrok['kode'],
                $bentrok['nama_kelas'],
                nama_hari((int) $bentrok['hari']),
                jam($bentrok['jam_mulai']),
                jam($bentrok['jam_selesai']),
                $alasan
            );
        }

        return null;
    }

    private function dataForm(string $title, ?array $row, bool $baru = false): array
    {
        return [
            'title'       => $title,
            'row'         => $row,
            'baru'        => $baru,
            'daftarTa'    => $this->ta->terbaru(),
            'mataKuliah'  => (new MataKuliahModel())->options(),
            'dosen'       => (new DosenModel())->options(),
        ];
    }

    private function rules(): array
    {
        return [
            'tahun_akademik_id' => ['label' => 'Tahun akademik', 'rules' => 'required|is_not_unique[tahun_akademik.id]'],
            'mata_kuliah_id'    => ['label' => 'Mata kuliah', 'rules' => 'required|is_not_unique[mata_kuliah.id]'],
            'dosen_id'          => ['label' => 'Dosen', 'rules' => 'required|is_not_unique[dosen.id]'],
            'nama_kelas'        => ['label' => 'Nama kelas', 'rules' => 'required|alpha_numeric|max_length[5]'],
            'hari'              => ['label' => 'Hari', 'rules' => 'required|in_list[1,2,3,4,5,6]'],
            'jam_mulai'         => ['label' => 'Jam mulai', 'rules' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]'],
            'jam_selesai'       => ['label' => 'Jam selesai', 'rules' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]'],
            'ruang'             => ['label' => 'Ruang', 'rules' => 'required|max_length[20]'],
            'kuota'             => ['label' => 'Kuota', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[200]'],
        ];
    }

    private function input(): array
    {
        $p = $this->request->getPost();

        return [
            'tahun_akademik_id' => (int) $p['tahun_akademik_id'],
            'mata_kuliah_id'    => (int) $p['mata_kuliah_id'],
            'dosen_id'          => (int) $p['dosen_id'],
            'nama_kelas'        => strtoupper(trim($p['nama_kelas'])),
            'hari'              => (int) $p['hari'],
            'jam_mulai'         => substr($p['jam_mulai'], 0, 5) . ':00',
            'jam_selesai'       => substr($p['jam_selesai'], 0, 5) . ':00',
            'ruang'             => trim($p['ruang']),
            'kuota'             => (int) $p['kuota'],
        ];
    }
}
