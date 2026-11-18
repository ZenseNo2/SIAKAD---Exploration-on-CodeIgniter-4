<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Peta URL SIAKAD.
 * Filter:  auth  = harus login,  guest = belum login,  role:x = hanya peran x
 *
 * @var RouteCollection $routes
 */

$routes->get('/', static fn () => redirect()->to(session('user') ? '/dashboard' : '/login'));

// ------------------------------------------------------------------ Login
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attempt');
});
$routes->post('logout', 'Auth::logout');

// ------------------------------------------------- Halaman yang perlu login
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('akun/password', 'Akun::password');
    $routes->post('akun/password', 'Akun::updatePassword');

    /**
     * Rute CRUD standar untuk satu resource:
     *   GET  /path              index   (daftar)
     *   GET  /path/new          new     (form tambah)
     *   POST /path              create  (simpan baru)
     *   GET  /path/{id}/edit    edit    (form ubah)
     *   POST /path/{id}         update  (simpan perubahan)
     *   POST /path/{id}/delete  delete  (hapus)
     */
    $crud = static function ($routes, string $path, string $controller) {
        $routes->get($path, "{$controller}::index");
        $routes->get("{$path}/new", "{$controller}::new");
        $routes->post($path, "{$controller}::create");
        $routes->get("{$path}/(:num)/edit", "{$controller}::edit/$1");
        $routes->post("{$path}/(:num)", "{$controller}::update/$1");
        $routes->post("{$path}/(:num)/delete", "{$controller}::delete/$1");
    };

    // ------------------------------------------------------------- Admin
    $routes->group('admin', ['filter' => 'role:admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) use ($crud) {
        $crud($routes, 'prodi', 'Prodi');
        $crud($routes, 'dosen', 'Dosen');
        $crud($routes, 'mahasiswa', 'Mahasiswa');
        $crud($routes, 'mata-kuliah', 'MataKuliah');
        $crud($routes, 'kelas', 'Kelas');
        $routes->get('kelas/(:num)', 'Kelas::show/$1');
        $routes->post('mahasiswa/(:num)/reset-password', 'Mahasiswa::resetPassword/$1');
        $routes->post('dosen/(:num)/reset-password', 'Dosen::resetPassword/$1');

        $routes->get('tahun-akademik', 'TahunAkademik::index');
        $routes->post('tahun-akademik', 'TahunAkademik::create');
        $routes->post('tahun-akademik/(:num)/aktifkan', 'TahunAkademik::aktifkan/$1');
        $routes->post('tahun-akademik/(:num)/krs', 'TahunAkademik::toggleKrs/$1');
        $routes->post('tahun-akademik/(:num)/delete', 'TahunAkademik::delete/$1');
    });

    // ------------------------------------------------------------- Dosen
    $routes->group('dosen', ['filter' => 'role:dosen', 'namespace' => 'App\Controllers\Dosen'], static function ($routes) {
        $routes->get('kelas', 'Kelas::index');
        $routes->get('kelas/(:num)', 'Kelas::nilai/$1');
        $routes->post('kelas/(:num)', 'Kelas::simpanNilai/$1');
        $routes->get('perwalian', 'Perwalian::index');
        $routes->get('perwalian/(:num)', 'Perwalian::show/$1');
    });

    // --------------------------------------------------------- Mahasiswa
    $routes->group('mahasiswa', ['filter' => 'role:mahasiswa', 'namespace' => 'App\Controllers\Mahasiswa'], static function ($routes) {
        $routes->get('krs', 'Krs::index');
        $routes->post('krs/ambil', 'Krs::ambil');
        $routes->post('krs/(:num)/batal', 'Krs::batal/$1');
        $routes->get('jadwal', 'Jadwal::index');
        $routes->get('khs', 'Khs::index');
        $routes->get('transkrip', 'Transkrip::index');
    });
});
