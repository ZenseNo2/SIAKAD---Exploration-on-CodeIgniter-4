<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Libraries\AturanKrs;
use App\Libraries\IndeksPrestasi;
use App\Models\KelasModel;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\TahunAkademikModel;

/**
 * Pengisian Kartu Rencana Studi (KRS) untuk semester aktif.
 */
class Krs extends BaseController
{
    private KrsModel $krs;
    private ?array $ta;
    private ?array $mhs = null;

    public function __construct()
    {
        $this->krs = new KrsModel();
        $this->ta  = (new TahunAkademikModel())->aktif();
    }

    public function index()
    {
        $mhs = $this->mahasiswa();
        if (! $this->ta) {
            return view('mahasiswa/krs', ['title' => 'KRS', 'ta' => null]);
        }

        $diambil  = $this->krs->milikMahasiswa($mhs['id'], (int) $this->ta['id']);
        $batas    = (new IndeksPrestasi())->batasSks($mhs['id'], (int) $this->ta['id']);
        $kelasIds = array_column($diambil, 'kelas_id');
        $mkIds    = array_column($diambil, 'mata_kuliah_id');

        // Kelas yang ditawarkan untuk prodi mahasiswa, dengan status apakah bisa diambil
        $aturan   = new AturanKrs();
        $tersedia = [];
        foreach ((new KelasModel())->byTahun((int) $this->ta['id'], (int) $mhs['prodi_id']) as $k) {
            if (in_array($k['id'], $kelasIds)) {
                continue;
            }
            $k['alasan'] = in_array($k['mata_kuliah_id'], $mkIds)
                ? 'Sudah diambil di kelas lain'
                : $aturan->periksa($mhs, $k, $this->ta, $diambil, $batas['batas']);
            $tersedia[] = $k;
        }
        usort($tersedia, static fn ($a, $b) => [$a['mk_semester'], $a['kode'], $a['nama_kelas']] <=> [$b['mk_semester'], $b['kode'], $b['nama_kelas']]);

        return view('mahasiswa/krs', [
            'title'    => 'Kartu Rencana Studi',
            'ta'       => $this->ta,
            'mhs'      => $mhs,
            'diambil'  => $diambil,
            'tersedia' => $tersedia,
            'totalSks' => array_sum(array_column($diambil, 'sks')),
            'batas'    => $batas,
        ]);
    }

    public function ambil()
    {
        $mhs = $this->mahasiswa();
        if (! $this->ta) {
            return redirect()->back()->with('error', 'Belum ada semester aktif.');
        }

        $kelas = (new KelasModel())->findDetail((int) $this->request->getPost('kelas_id'));
        if (! $kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $diambil = $this->krs->milikMahasiswa($mhs['id'], (int) $this->ta['id']);
        $batas   = (new IndeksPrestasi())->batasSks($mhs['id'], (int) $this->ta['id']);

        // Semua aturan (periode, prodi, duplikat, bentrok, kuota, batas SKS) dicek di sini
        if ($pesan = (new AturanKrs())->periksa($mhs, $kelas, $this->ta, $diambil, $batas['batas'])) {
            return redirect()->back()->with('error', $pesan);
        }

        $this->krs->insert(['mahasiswa_id' => $mhs['id'], 'kelas_id' => $kelas['id']]);

        return redirect()->to('/mahasiswa/krs')->with('success', "{$kelas['mk_nama']} (kelas {$kelas['nama_kelas']}) ditambahkan ke KRS.");
    }

    public function batal(int $krsId)
    {
        $mhs = $this->mahasiswa();
        $row = $this->krs->select('krs.*, kelas.tahun_akademik_id, mk.nama AS mk_nama')
            ->join('kelas', 'kelas.id = krs.kelas_id')
            ->join('mata_kuliah mk', 'mk.id = kelas.mata_kuliah_id')
            ->where('krs.id', $krsId)->where('krs.mahasiswa_id', $mhs['id'])
            ->first();

        if (! $row || ! $this->ta || (int) $row['tahun_akademik_id'] !== (int) $this->ta['id']) {
            return redirect()->back()->with('error', 'Data KRS tidak ditemukan.');
        }
        if (! $this->ta['krs_dibuka']) {
            return redirect()->back()->with('error', 'Periode KRS sudah ditutup.');
        }
        if ($row['nilai_huruf'] !== null) {
            return redirect()->back()->with('error', 'Mata kuliah yang sudah dinilai tidak dapat dibatalkan.');
        }

        $this->krs->delete($krsId);

        return redirect()->to('/mahasiswa/krs')->with('success', "{$row['mk_nama']} dihapus dari KRS.");
    }

    private function mahasiswa(): array
    {
        return $this->mhs ??= (new MahasiswaModel())->find($this->user['mahasiswa_id']);
    }
}
