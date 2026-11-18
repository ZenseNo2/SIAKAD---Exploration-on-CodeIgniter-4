<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Aturan akademik yang dipakai di seluruh aplikasi.
 * Ubah di sini bila kampus memakai skala atau bobot yang berbeda.
 * Diakses dengan: config('Akademik')
 */
class Akademik extends BaseConfig
{
    /** Bobot komponen nilai akhir (persen, total 100). */
    public array $bobot = [
        'tugas' => 30,
        'uts'   => 30,
        'uas'   => 40,
    ];

    /**
     * Skala konversi nilai angka ke huruf, diurutkan dari yang tertinggi.
     * min = batas bawah nilai akhir untuk mendapat huruf tersebut.
     */
    public array $skala = [
        ['min' => 85, 'huruf' => 'A',  'bobot' => 4.0],
        ['min' => 75, 'huruf' => 'AB', 'bobot' => 3.5],
        ['min' => 65, 'huruf' => 'B',  'bobot' => 3.0],
        ['min' => 60, 'huruf' => 'BC', 'bobot' => 2.5],
        ['min' => 55, 'huruf' => 'C',  'bobot' => 2.0],
        ['min' => 40, 'huruf' => 'D',  'bobot' => 1.0],
        ['min' => 0,  'huruf' => 'E',  'bobot' => 0.0],
    ];

    /** Huruf yang dianggap tidak lulus (SKS-nya tidak dihitung sebagai SKS lulus). */
    public array $tidakLulus = ['E'];

    /**
     * Batas SKS yang boleh diambil di KRS berdasarkan IPS semester sebelumnya.
     * Dicek dari atas: IPS >= minIps mendapat jatah sks.
     */
    public array $batasSks = [
        ['minIps' => 3.00, 'sks' => 24],
        ['minIps' => 2.50, 'sks' => 21],
        ['minIps' => 2.00, 'sks' => 18],
        ['minIps' => 0.00, 'sks' => 15],
    ];

    /** Jatah SKS untuk mahasiswa yang belum punya IPS (mahasiswa baru). */
    public int $sksMahasiswaBaru = 20;

    /** Nama hari untuk kolom kelas.hari */
    public array $hari = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];
}
