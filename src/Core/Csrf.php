<?php
namespace Simit\Core;

final class Csrf
{
    public static function token(): string { return $_SESSION['_csrf'] ??= bin2hex(random_bytes(32)); }
    public static function field(): string { return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">'; }
    public static function verify(): void
    {
        if (!hash_equals($_SESSION['_csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
            http_response_code(419); exit('Sesi formulir kedaluwarsa. Muat ulang halaman.');
        }
    }
}

