<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh SIAKAD.
 * Jalankan: php spark db:seed DatabaseSeeder
 *
 * Akun login:
 *   admin      → admin / admin123
 *   dosen      → NIDN  / NIDN      (contoh 0712058801 / 0712058801)
 *   mahasiswa  → NIM   / NIM       (contoh 25110001 / 25110001)
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        helper('akademik');
        mt_srand(2026); // nilai acak tapi selalu sama setiap seeding

        $now = date('Y-m-d H:i:s');
        $db  = $this->db;

        // ----------------------------------------------------------- prodi
        $prodi = [];
        foreach ([['IF', 'Informatika'], ['SI', 'Sistem Informasi']] as [$kode, $nama]) {
            $db->table('prodi')->insert(['kode' => $kode, 'nama' => $nama, 'jenjang' => 'S1']);
            $prodi[$kode] = $db->insertID();
        }

        // ----------------------------------------------------------- dosen
        $dosenData = [
            ['0712058801', 'Dr. Rahmat Hidayat, M.Kom.', 'IF'],
            ['0721039002', 'Siti Nurhaliza, M.T.', 'IF'],
            ['0803118503', 'Agus Prasetyo, M.Cs.', 'IF'],
            ['0715079104', 'Dewi Lestari, M.Kom.', 'SI'],
            ['0909128705', 'Bambang Sutrisno, M.M.S.I.', 'SI'],
            ['0730069306', 'Fitri Handayani, M.Kom.', 'SI'],
        ];
        $dosen = [];
        foreach ($dosenData as [$nidn, $nama, $p]) {
            $db->table('dosen')->insert([
                'nidn' => $nidn, 'nama' => $nama, 'prodi_id' => $prodi[$p],
                'email' => strtolower(explode(' ', str_replace('Dr. ', '', $nama))[0]) . '@kampus.ac.id',
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $dosen[$nidn] = $db->insertID();
            $this->akun($nidn, $nama, 'dosen', dosenId: $dosen[$nidn]);
        }

        // ------------------------------------------------------- mahasiswa
        $mhsData = [
            ['25110001', 'Arif Budiman', 'L', 'IF', '0712058801'],
            ['25110002', 'Nabila Putri Ramadhani', 'P', 'IF', '0712058801'],
            ['25110003', 'Yoga Pratama', 'L', 'IF', '0721039002'],
            ['25110004', 'Salsabila Azzahra', 'P', 'IF', '0721039002'],
            ['25110005', 'Rizky Maulana', 'L', 'IF', '0803118503'],
            ['25110006', 'Aulia Rahmawati', 'P', 'IF', '0803118503'],
            ['25110007', 'Dimas Saputra', 'L', 'IF', '0712058801'],
            ['25120001', 'Intan Permatasari', 'P', 'SI', '0715079104'],
            ['25120002', 'Fajar Nugroho', 'L', 'SI', '0715079104'],
            ['25120003', 'Ayu Kartika Sari', 'P', 'SI', '0909128705'],
            ['25120004', 'Bagas Wicaksono', 'L', 'SI', '0909128705'],
            ['25120005', 'Nadia Safitri', 'P', 'SI', '0730069306'],
        ];
        $mhs = []; // nim => [id, prodi]
        foreach ($mhsData as [$nim, $nama, $jk, $p, $wali]) {
            $db->table('mahasiswa')->insert([
                'nim' => $nim, 'nama' => $nama, 'prodi_id' => $prodi[$p], 'angkatan' => 2025,
                'jenis_kelamin' => $jk, 'email' => $nim . '@student.kampus.ac.id',
                'dosen_wali_id' => $dosen[$wali], 'created_at' => $now, 'updated_at' => $now,
            ]);
            $mhs[$nim] = ['id' => $db->insertID(), 'prodi' => $p];
            $this->akun($nim, $nama, 'mahasiswa', mahasiswaId: $mhs[$nim]['id']);
        }

        // ------------------------------------------------------ admin
        $this->akun('admin', 'Administrator Akademik', 'admin', password: 'admin123');

        // ------------------------------------------------- mata kuliah
        $mkData = [
            // kode, nama, sks, semester, prodi
            ['IF101', 'Algoritma dan Pemrograman', 4, 1, 'IF'],
            ['IF102', 'Matematika Diskrit', 3, 1, 'IF'],
            ['IF103', 'Pengantar Teknologi Informasi', 2, 1, 'IF'],
            ['IF104', 'Kalkulus', 3, 1, 'IF'],
            ['IF201', 'Struktur Data', 4, 2, 'IF'],
            ['IF202', 'Basis Data', 3, 2, 'IF'],
            ['IF203', 'Aljabar Linear', 3, 2, 'IF'],
            ['IF301', 'Pemrograman Web', 3, 3, 'IF'],
            ['IF302', 'Sistem Operasi', 3, 3, 'IF'],
            ['IF303', 'Jaringan Komputer', 3, 3, 'IF'],
            ['IF304', 'Statistika dan Probabilitas', 3, 3, 'IF'],
            ['IF305', 'Pemrograman Berorientasi Objek', 3, 3, 'IF'],
            ['SI101', 'Pengantar Sistem Informasi', 3, 1, 'SI'],
            ['SI102', 'Algoritma Pemrograman', 3, 1, 'SI'],
            ['SI103', 'Dasar Manajemen', 2, 1, 'SI'],
            ['SI201', 'Sistem Basis Data', 3, 2, 'SI'],
            ['SI202', 'Akuntansi Dasar', 3, 2, 'SI'],
            ['SI301', 'Analisis dan Perancangan Sistem', 3, 3, 'SI'],
            ['SI302', 'Basis Data Lanjut', 3, 3, 'SI'],
            ['SI303', 'Manajemen Proyek TI', 3, 3, 'SI'],
            ['SI304', 'E-Business', 2, 3, 'SI'],
        ];
        $mk = [];
        foreach ($mkData as [$kode, $nama, $sks, $smt, $p]) {
            $db->table('mata_kuliah')->insert([
                'kode' => $kode, 'nama' => $nama, 'sks' => $sks, 'semester' => $smt, 'prodi_id' => $prodi[$p],
            ]);
            $mk[$kode] = $db->insertID();
        }

        // ---------------------------------------------- tahun akademik
        $ta = [];
        foreach ([
            ['2025/2026', 'Ganjil', 0, 0],
            ['2025/2026', 'Genap', 0, 0],
            ['2026/2027', 'Ganjil', 1, 1], // aktif, KRS dibuka
        ] as $i => [$tahun, $smt, $aktif, $krs]) {
            $db->table('tahun_akademik')->insert([
                'tahun' => $tahun, 'semester' => $smt, 'is_active' => $aktif, 'krs_dibuka' => $krs,
            ]);
            $ta[$i] = $db->insertID();
        }

        // ------------------------------------------------------- kelas
        // [ta index, kode mk, nidn, kelas, hari, mulai, selesai, ruang]
        $kelasData = [
            // 2025/2026 Ganjil
            [0, 'IF101', '0712058801', 'A', 1, '07:30', '10:50', 'Lab 1'],
            [0, 'IF102', '0803118503', 'A', 2, '07:30', '10:00', 'R.201'],
            [0, 'IF103', '0721039002', 'A', 3, '13:00', '14:40', 'R.202'],
            [0, 'IF104', '0803118503', 'A', 4, '07:30', '10:00', 'R.201'],
            [0, 'SI101', '0715079104', 'A', 1, '10:00', '12:30', 'R.301'],
            [0, 'SI102', '0909128705', 'A', 2, '13:00', '15:30', 'Lab 2'],
            [0, 'SI103', '0730069306', 'A', 4, '10:00', '11:40', 'R.302'],
            // 2025/2026 Genap
            [1, 'IF201', '0712058801', 'A', 1, '07:30', '10:50', 'Lab 1'],
            [1, 'IF202', '0721039002', 'A', 3, '07:30', '10:00', 'Lab 2'],
            [1, 'IF203', '0803118503', 'A', 5, '07:30', '10:00', 'R.201'],
            [1, 'SI201', '0715079104', 'A', 2, '10:00', '12:30', 'Lab 2'],
            [1, 'SI202', '0909128705', 'A', 4, '13:00', '15:30', 'R.301'],
            // 2026/2027 Ganjil (aktif)
            [2, 'IF301', '0712058801', 'A', 1, '07:30', '10:00', 'Lab 1'],
            [2, 'IF301', '0712058801', 'B', 2, '13:00', '15:30', 'Lab 1'],
            [2, 'IF302', '0721039002', 'A', 2, '07:30', '10:00', 'R.201'],
            [2, 'IF303', '0803118503', 'A', 3, '10:00', '12:30', 'Lab 3'],
            [2, 'IF304', '0803118503', 'A', 4, '07:30', '10:00', 'R.202'],
            [2, 'IF305', '0721039002', 'A', 2, '14:00', '16:30', 'Lab 2'], // sengaja beririsan dengan IF301 B
            [2, 'SI301', '0715079104', 'A', 1, '10:00', '12:30', 'R.301'],
            [2, 'SI302', '0730069306', 'A', 3, '07:30', '10:00', 'Lab 2'],
            [2, 'SI303', '0909128705', 'A', 4, '10:00', '12:30', 'R.302'],
            [2, 'SI304', '0730069306', 'A', 5, '13:00', '14:40', 'R.302'],
        ];
        $kelas = []; // "ta|kode|kelas" => id
        foreach ($kelasData as [$t, $kode, $nidn, $nk, $hari, $mulai, $selesai, $ruang]) {
            $db->table('kelas')->insert([
                'tahun_akademik_id' => $ta[$t], 'mata_kuliah_id' => $mk[$kode], 'dosen_id' => $dosen[$nidn],
                'nama_kelas' => $nk, 'hari' => $hari, 'jam_mulai' => $mulai . ':00', 'jam_selesai' => $selesai . ':00',
                'ruang' => $ruang, 'kuota' => 40,
            ]);
            $kelas["{$t}|{$kode}|{$nk}"] = $db->insertID();
        }

        // --------------------------------------------- KRS + nilai riwayat
        // Semester lalu: semua mahasiswa mengambil seluruh kelas prodinya dan sudah dinilai.
        foreach ($mhs as $m) {
            foreach ($kelas as $key => $kelasId) {
                [$t, $kode] = explode('|', $key);
                if ($t === '2' || substr($kode, 0, 2) !== $m['prodi']) {
                    continue;
                }
                $tugas = mt_rand(6500, 9600) / 100;
                $uts   = mt_rand(5000, 9500) / 100;
                $uas   = mt_rand(4800, 9500) / 100;
                $akhir = hitung_nilai_akhir($tugas, $uts, $uas);
                $db->table('krs')->insert([
                    'mahasiswa_id' => $m['id'], 'kelas_id' => $kelasId,
                    'nilai_tugas' => $tugas, 'nilai_uts' => $uts, 'nilai_uas' => $uas,
                    'nilai_akhir' => $akhir, 'nilai_huruf' => nilai_huruf($akhir),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // Semester aktif: sebagian mahasiswa sudah mengisi KRS (belum ada nilai).
        $krsAktif = [
            '25110001' => ['IF301|A', 'IF302|A'],                         // baru sebagian, bisa ditambah
            '25110002' => ['IF301|A', 'IF302|A', 'IF303|A', 'IF304|A'],
            '25110003' => ['IF301|A', 'IF302|A', 'IF303|A', 'IF304|A'],
            '25110004' => ['IF301|B', 'IF303|A', 'IF304|A'],
            '25120001' => ['SI301|A', 'SI302|A', 'SI303|A'],
            '25120002' => ['SI301|A', 'SI302|A', 'SI303|A', 'SI304|A'],
        ];
        foreach ($krsAktif as $nim => $list) {
            foreach ($list as $item) {
                [$kode, $nk] = explode('|', $item);
                $db->table('krs')->insert([
                    'mahasiswa_id' => $mhs[$nim]['id'], 'kelas_id' => $kelas["2|{$kode}|{$nk}"],
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    /** Membuat akun login. Password default = username, kecuali ditentukan. */
    private function akun(string $username, string $nama, string $role, ?int $dosenId = null, ?int $mahasiswaId = null, ?string $password = null): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('users')->insert([
            'username'      => $username,
            'password_hash' => password_hash($password ?? $username, PASSWORD_DEFAULT),
            'nama'          => $nama,
            'role'          => $role,
            'dosen_id'      => $dosenId,
            'mahasiswa_id'  => $mahasiswaId,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }
}
