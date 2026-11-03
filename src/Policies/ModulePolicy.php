<?php
namespace Simit\Policies;

final class ModulePolicy
{
    private const USER_WRITABLE = ['pc', 'perangkat', 'software'];

    public static function create(array $user, string $module): bool
    {
        return self::admin($user) || in_array($module, self::USER_WRITABLE, true) || $module === 'tugas';
    }

    public static function update(array $user, string $module): bool
    {
        return self::admin($user) || in_array($module, self::USER_WRITABLE, true);
    }

    public static function delete(array $user, string $module): bool { return self::admin($user); }
    private static function admin(array $user): bool { return ($user['role'] ?? '') === 'admin'; }
}
