<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attempt()
    {
        // Batasi 5 percobaan per menit untuk kombinasi IP + username, untuk menghambat tebak-tebakan
        // password. Username ikut dihitung agar satu lab kampus (satu IP publik) tidak saling memblokir.
        $throttler = service('throttler');
        $kunci = md5('login|' . $this->request->getIPAddress() . '|' . strtolower(trim((string) $this->request->getPost('username'))));
        if ($throttler->check($kunci, 5, MINUTE) === false) {
            return $this->kembaliDenganError('Terlalu banyak percobaan. Coba lagi dalam ' . $throttler->getTokenTime() . ' detik.');
        }

        if (! $this->validate(['username' => 'required', 'password' => 'required'])) {
            return $this->kembaliDenganError();
        }

        $users = new UserModel();
        $user  = $users->findByUsername(trim((string) $this->request->getPost('username')));

        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return $this->kembaliDenganError('Username atau password salah.');
        }

        // Cegah session fixation, lalu simpan data penting di session
        session()->regenerate(true);
        session()->set('user', [
            'id'           => (int) $user['id'],
            'username'     => $user['username'],
            'nama'         => $user['nama'],
            'role'         => $user['role'],
            'dosen_id'     => $user['dosen_id'] ? (int) $user['dosen_id'] : null,
            'mahasiswa_id' => $user['mahasiswa_id'] ? (int) $user['mahasiswa_id'] : null,
        ]);
        $users->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}
