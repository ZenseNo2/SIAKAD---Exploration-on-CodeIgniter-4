<?php

namespace App\Libraries;

use App\Models\KrsModel;

/**
 * Perhitungan IPS, IPK, dan batas SKS.
 *
 *   IP = Σ (SKS × bobot huruf) / Σ SKS
 *
 * - IPS : hanya mata kuliah pada satu tahun akademik.
 * - IPK : seluruh mata kuliah. Jika satu mata kuliah diambil lebih dari sekali
 *         (mengulang), yang dihitung adalah nilai terbaiknya.
 * - Mata kuliah yang belum dinilai tidak ikut dihitung.
 */
class IndeksPrestasi
{
    private KrsModel $krs;

    public function __construct(?KrsModel $krs = null)
    {
        helper('akademik');
        $this->krs = $krs ?? new KrsModel();
    }

    /**
     * Menghitung IP dari kumpulan baris KRS (harus punya kolom sks & nilai_huruf).
     *
     * @return array{ip: float|null, sks: int, mutu: float, sks_lulus: int}
     */
    public static function hitung(array $rows): array
    {
        helper('akademik');
        $tidakLulus = config('Akademik')->tidakLulus;
        $sks = $sksLulus = 0;
        $mutu = 0.0;

        foreach ($rows as $r) {
            if ($r['nilai_huruf'] === null) {
                continue;
            }
            $sks  += (int) $r['sks'];
            $mutu += (int) $r['sks'] * bobot_huruf($r['nilai_huruf']);
            if (! in_array($r['nilai_huruf'], $tidakLulus, true)) {
                $sksLulus += (int) $r['sks'];
            }
        }

        return [
            'ip'        => $sks > 0 ? round($mutu / $sks, 2) : null,
            'sks'       => $sks,
            'mutu'      => $mutu,
            'sks_lulus' => $sksLulus,
        ];
    }

    /** Ambil nilai terbaik untuk tiap mata kuliah (dipakai IPK & transkrip). */
    public static function nilaiTerbaik(array $rows): array
    {
        helper('akademik');
        $terbaik = [];
        foreach ($rows as $r) {
            if ($r['nilai_huruf'] === null) {
                continue;
            }
            $id = $r['mata_kuliah_id'];
            if (! isset($terbaik[$id]) || bobot_huruf($r['nilai_huruf']) > bobot_huruf($terbaik[$id]['nilai_huruf'])) {
                $terbaik[$id] = $r;
            }
        }
        uasort($terbaik, static fn ($a, $b) => [$a['mk_semester'], $a['kode']] <=> [$b['mk_semester'], $b['kode']]);

        return array_values($terbaik);
    }

    public function ips(int $mahasiswaId, int $taId): array
    {
        return self::hitung($this->krs->milikMahasiswa($mahasiswaId, $taId));
    }

    public function ipk(int $mahasiswaId): array
    {
        return self::hitung(self::nilaiTerbaik($this->krs->riwayat($mahasiswaId)));
    }

    /**
     * Rekap per semester, urut kronologis, termasuk IPK kumulatif sampai semester itu.
     *
     * @return list<array{ta_id:int, label:string, rows:array, ips:float|null, sks:int, ipk:float|null}>
     */
    public function perSemester(int $mahasiswaId): array
    {
        $grup = [];
        foreach ($this->krs->riwayat($mahasiswaId) as $r) {
            $grup[$r['tahun_akademik_id']]['label'] = "{$r['tahun']} {$r['ta_semester']}";
            $grup[$r['tahun_akademik_id']]['rows'][] = $r;
        }

        $hasil = [];
        $kumulatif = [];
        foreach ($grup as $taId => $g) {
            $kumulatif = array_merge($kumulatif, $g['rows']);
            $ips = self::hitung($g['rows']);
            $hasil[] = [
                'ta_id' => (int) $taId,
                'label' => $g['label'],
                'rows'  => $g['rows'],
                'ips'   => $ips['ip'],
                'sks'   => $ips['sks'],
                'ipk'   => self::hitung(self::nilaiTerbaik($kumulatif))['ip'],
            ];
        }

        return $hasil;
    }

    /**
     * Batas SKS di KRS semester aktif, ditentukan oleh IPS semester terakhir yang sudah dinilai.
     *
     * @return array{batas:int, ips:float|null, dasar:string}
     */
    public function batasSks(int $mahasiswaId, int $taAktifId): array
    {
        $config = config('Akademik');
        $terakhir = null;
        foreach ($this->perSemester($mahasiswaId) as $s) {
            if ($s['ta_id'] !== $taAktifId && $s['ips'] !== null) {
                $terakhir = $s; // perSemester sudah kronologis → yang terakhir adalah yang terbaru
            }
        }

        if ($terakhir === null) {
            return ['batas' => $config->sksMahasiswaBaru, 'ips' => null, 'dasar' => 'Mahasiswa baru'];
        }

        foreach ($config->batasSks as $aturan) {
            if ($terakhir['ips'] >= $aturan['minIps']) {
                return ['batas' => $aturan['sks'], 'ips' => $terakhir['ips'], 'dasar' => 'IPS ' . $terakhir['label']];
            }
        }

        return ['batas' => end($config->batasSks)['sks'], 'ips' => $terakhir['ips'], 'dasar' => 'IPS ' . $terakhir['label']];
    }
}
