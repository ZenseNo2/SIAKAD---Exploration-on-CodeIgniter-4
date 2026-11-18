<?php

namespace App\Controllers;

use App\Models\UserModel;

class Akun extends BaseController
{
    public function password()
    {
        return view('akun/password', ['title' => 'Ganti Password']);
    }

    public function updatePassword()
    {
        $rules = [
            'password_lama'       => ['label' => 'Password lama', 'rules' => 'required'],
            'password_baru'       => ['label' => 'Password baru', 'rules' => 'required|min_length[6]|max_length[72]'],
            'konfirmasi_password' => ['label' => 'Konfirmasi password', 'rules' => 'required|matches[password_baru]'],
        ];
        if (! $this->validate($rules)) {
            return $this->kembaliDenganError();
        }

        $users = new UserModel();
        $user  = $users->find($this->user['id']);

        if (! password_verify((string) $this->request->getPost('password_lama'), $user['password_hash'])) {
            return $this->kembaliDenganError('Password lama tidak sesuai.');
        }

        $users->update($user['id'], [
            'password_hash' => password_hash((string) $this->request->getPost('password_baru'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/akun/password')->with('success', 'Password berhasil diganti.');
    }
}
