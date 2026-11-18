<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Menolak akses jika belum login.
 * Dipasang di Routes: ['filter' => 'auth']
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('user')) {
            return redirect()->to('/login')->with('error', 'Silakan masuk terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Halaman yang butuh login tidak boleh disimpan di cache browser (tombol Back setelah logout)
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
