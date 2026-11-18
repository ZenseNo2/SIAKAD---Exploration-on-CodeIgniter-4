<?php

namespace App\Libraries;

/**
 * Aturan bisnis pengambilan kelas di KRS.
 * Dipisah dari controller agar mudah dibaca, diuji, dan diubah.
 */
class AturanKrs
{
    /**
     * Memeriksa apakah mahasiswa boleh mengambil sebuah kelas.
     *
     * @param array $mahasiswa  baris tabel mahasiswa
     * @param array $kelas      hasil KelasModel::findDetail()
     * @param array $ta         tahun akademik aktif
     * @param array $krsSaatIni hasil KrsModel::milikMahasiswa() untuk semester aktif
     * @param int   $batasSks   hasil IndeksPrestasi::batasSks()
     *
     * @return string|null  pesan kesalahan, atau null jika boleh
     */
    public function periksa(array $mahasiswa, array $kelas, array $ta, array $krsSaatIni, int $batasSks): ?string
    {
        helper('akademik');

        // 1. Periode KRS harus dibuka
        if (! $ta['krs_dibuka']) {
            return 'Periode pengisian KRS sedang ditutup.';
        }

        // 2. Kelas harus milik semester aktif
        if ((int) $kelas['tahun_akademik_id'] !== (int) $ta['id']) {
            return 'Kelas ini bukan bagian dari semester aktif.';
        }

        // 3. Mata kuliah harus dari prodi mahasiswa
        if ((int) $kelas['prodi_id'] !== (int) $mahasiswa['prodi_id']) {
            return 'Mata kuliah ini bukan milik program studi Anda.';
        }

        $totalSks = 0;
        foreach ($krsSaatIni as $k) {
            // 4. Tidak boleh mengambil mata kuliah yang sama dua kali (meski beda kelas)
            if ((int) $k['mata_kuliah_id'] === (int) $kelas['mata_kuliah_id']) {
                return "{$kelas['mk_nama']} sudah ada di KRS Anda (kelas {$k['nama_kelas']}).";
            }

            // 5. Jadwal tidak boleh bentrok
            if (jadwal_bentrok((int) $k['hari'], $k['jam_mulai'], $k['jam_selesai'], (int) $kelas['hari'], $kelas['jam_mulai'], $kelas['jam_selesai'])) {
                return sprintf(
                    'Jadwal bentrok dengan %s (%s, %s–%s).',
                    $k['mk_nama'],
                    nama_hari((int) $k['hari']),
                    jam($k['jam_mulai']),
                    jam($k['jam_selesai'])
                );
            }
            $totalSks += (int) $k['sks'];
        }

        // 6. Kuota kelas
        if ((int) $kelas['jumlah_peserta'] >= (int) $kelas['kuota']) {
            return "Kelas {$kelas['kode']} {$kelas['nama_kelas']} sudah penuh.";
        }

        // 7. Batas SKS
        if ($totalSks + (int) $kelas['sks'] > $batasSks) {
            return "Melebihi batas SKS. Saat ini {$totalSks} SKS, batas Anda {$batasSks} SKS.";
        }

        return null;
    }
}
