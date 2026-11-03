<?php
namespace Simit\Policies;

final class ProfilePolicy
{
    public static function update(array $user, int $profileId): bool { return (int)$user['id'] === $profileId; }
    public static function viewAvatar(array $user): bool { return isset($user['id']); }
}
