<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    /**
     * Data pengguna yang sedang login (diisi saat login di Auth::attempt).
     * ['id', 'username', 'nama', 'role', 'dosen_id', 'mahasiswa_id']
     */
    protected ?array $user = null;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Helper yang dimuat di semua controller (dan otomatis tersedia di view)
        $this->helpers = ['form', 'url', 'akademik', 'ui'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $this->user = session('user');
    }

    /**
     * Redirect kembali ke form dengan input lama dan pesan validasi.
     */
    protected function kembaliDenganError(?string $pesan = null)
    {
        $redirect = redirect()->back()->withInput();

        return $pesan ? $redirect->with('error', $pesan) : $redirect;
    }
}
