<?php
namespace Simit\Policies;

final class AccountPolicy
{
    public static function manage(array $user): bool { return ($user['role'] ?? '') === 'admin'; }
}
