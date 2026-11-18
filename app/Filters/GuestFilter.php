<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Halaman login hanya untuk yang belum login.
 */
class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('user')) {
            return redirect()->to('/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
