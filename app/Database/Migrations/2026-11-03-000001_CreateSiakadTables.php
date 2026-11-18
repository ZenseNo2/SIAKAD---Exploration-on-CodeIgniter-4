<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Struktur database SIAKAD.
 *
 * prodi ─┬─< dosen ─────────────< kelas >──── mata_kuliah >── prodi
 *        └─< mahasiswa ──< krs >──┘   │
 *                                     └──── tahun_akademik
 * users  → menunjuk ke dosen / mahasiswa (akun login)
 *
 * Jalankan: php spark migrate
 */
class CreateSiakadTables extends Migration
{
    public function up()
    {
        // ------------------------------------------------------------ prodi
        $this->forge->addField([
            'id'      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode'    => ['type' => 'VARCHAR', 'constraint' => 10],
            'nama'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'jenjang' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'S1'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('prodi');

        // ------------------------------------------------------------ dosen
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nidn'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'prodi_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nidn');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('dosen');

        // -------------------------------------------------------- mahasiswa
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nim'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'prodi_id'      => ['type' => 'INT', 'unsigned' => true],
            'angkatan'      => ['type' => 'SMALLINT', 'unsigned' => true],
            'jenis_kelamin' => ['type' => 'CHAR', 'constraint' => 1],  // L / P
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'dosen_wali_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nim');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('dosen_wali_id', 'dosen', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('mahasiswa');

        // ------------------------------------------------------------ users
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'role'          => ['type' => 'VARCHAR', 'constraint' => 10],  // admin / dosen / mahasiswa
            'dosen_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'mahasiswa_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_login'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addForeignKey('dosen_id', 'dosen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');

        // ------------------------------------------------------ mata_kuliah
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode'     => ['type' => 'VARCHAR', 'constraint' => 15],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'sks'      => ['type' => 'TINYINT', 'unsigned' => true],
            'semester' => ['type' => 'TINYINT', 'unsigned' => true],
            'prodi_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('mata_kuliah');

        // --------------------------------------------------- tahun_akademik
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tahun'      => ['type' => 'VARCHAR', 'constraint' => 9],   // 2026/2027
            'semester'   => ['type' => 'VARCHAR', 'constraint' => 6],   // Ganjil / Genap
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'krs_dibuka' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['tahun', 'semester']);
        $this->forge->createTable('tahun_akademik');

        // ------------------------------------------------------------ kelas
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tahun_akademik_id' => ['type' => 'INT', 'unsigned' => true],
            'mata_kuliah_id'    => ['type' => 'INT', 'unsigned' => true],
            'dosen_id'          => ['type' => 'INT', 'unsigned' => true],
            'nama_kelas'        => ['type' => 'VARCHAR', 'constraint' => 5],
            'hari'              => ['type' => 'TINYINT', 'unsigned' => true], // 1 = Senin ... 6 = Sabtu
            'jam_mulai'         => ['type' => 'TIME'],
            'jam_selesai'       => ['type' => 'TIME'],
            'ruang'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'kuota'             => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 40],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tahun_akademik_id', 'tahun_akademik', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('mata_kuliah_id', 'mata_kuliah', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('dosen_id', 'dosen', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('kelas');

        // -------------------------------------------------------------- krs
        // Satu baris = satu mahasiswa mengambil satu kelas. Nilai disimpan di baris yang sama.
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mahasiswa_id' => ['type' => 'INT', 'unsigned' => true],
            'kelas_id'     => ['type' => 'INT', 'unsigned' => true],
            'nilai_tugas'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_uts'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_uas'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_akhir'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_huruf'  => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['mahasiswa_id', 'kelas_id']);
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('krs');
    }

    public function down()
    {
        // Urutan kebalikan agar foreign key tidak menghalangi
        foreach (['krs', 'kelas', 'tahun_akademik', 'mata_kuliah', 'users', 'mahasiswa', 'dosen', 'prodi'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
