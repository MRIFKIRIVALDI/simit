<?php
namespace Simit\Policies;

use Simit\Core\View;

final class Gate
{
    public static function authorize(bool $allowed, string $message = 'Anda tidak memiliki izin untuk mengakses data ini.', bool $hide = false): void
    {
        if ($allowed) return;
        http_response_code($hide ? 404 : 403);
        View::render('crud/error', [
            'title' => $hide ? 'Tidak ditemukan' : 'Akses ditolak',
            'message' => $hide ? 'Data tidak ditemukan.' : $message,
        ]);
        exit;
    }
}
