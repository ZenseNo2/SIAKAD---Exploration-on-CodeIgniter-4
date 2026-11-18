<?php
/**
 * Daftar menu per peran. Dipakai oleh sidebar (desktop) dan tab bar (mobile).
 * [url, label, ikon, warna tile, tampil di tab bar?]
 */
$menu = match (current_user('role')) {
    'admin' => [
        'Umum' => [
            ['dashboard', 'Ringkasan', 'home', 't-gray', true],
        ],
        'Data Master' => [
            ['admin/mahasiswa', 'Mahasiswa', 'users', 't-green', true],
            ['admin/dosen', 'Dosen', 'idcard', 't-orange', true],
            ['admin/prodi', 'Program Studi', 'building', 't-indigo', false],
            ['admin/mata-kuliah', 'Mata Kuliah', 'book', 't-pink', false],
        ],
        'Perkuliahan' => [
            ['admin/tahun-akademik', 'Tahun Akademik', 'clock', 't-red', false],
            ['admin/kelas', 'Jadwal Kelas', 'calendar', 't-blue', true],
        ],
    ],
    'dosen' => [
        'Umum' => [
            ['dashboard', 'Ringkasan', 'home', 't-gray', true],
        ],
        'Mengajar' => [
            ['dosen/kelas', 'Kelas & Nilai', 'chart', 't-blue', true],
            ['dosen/perwalian', 'Perwalian', 'users', 't-green', true],
        ],
    ],
    'mahasiswa' => [
        'Umum' => [
            ['dashboard', 'Ringkasan', 'home', 't-gray', true],
        ],
        'Perkuliahan' => [
            ['mahasiswa/krs', 'KRS', 'checklist', 't-blue', true],
            ['mahasiswa/jadwal', 'Jadwal Kuliah', 'calendar', 't-red', true],
        ],
        'Hasil Studi' => [
            ['mahasiswa/khs', 'KHS', 'doc', 't-orange', true],
            ['mahasiswa/transkrip', 'Transkrip', 'cap', 't-indigo', false],
        ],
    ],
    default => [],
};
$menu['Akun'] = [['akun/password', 'Ganti Password', 'key', 't-gray', false]];
