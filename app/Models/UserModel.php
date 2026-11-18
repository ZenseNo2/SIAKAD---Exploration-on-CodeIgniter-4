<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['username', 'password_hash', 'nama', 'role', 'dosen_id', 'mahasiswa_id', 'last_login'];

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /** Membuat akun baru. Password awal = username (wajib diganti pengguna). */
    public function buatAkun(string $username, string $nama, string $role, ?int $dosenId = null, ?int $mahasiswaId = null): int
    {
        return (int) $this->insert([
            'username'      => $username,
            'password_hash' => password_hash($username, PASSWORD_DEFAULT),
            'nama'          => $nama,
            'role'          => $role,
            'dosen_id'      => $dosenId,
            'mahasiswa_id'  => $mahasiswaId,
        ]);
    }
}
