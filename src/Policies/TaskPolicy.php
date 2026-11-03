<?php
namespace Simit\Policies;

final class TaskPolicy
{
    public static function view(array $user, array $task): bool
    {
        return self::admin($user)
            || $task['assignee_id'] === null
            || (int)$task['assignee_id'] === (int)$user['id']
            || (int)($task['created_by'] ?? 0) === (int)$user['id']
            || (int)($task['is_contributor'] ?? 0) === 1;
    }

    public static function submit(array $user, array $task): bool
    {
        return self::admin($user)
            || $task['assignee_id'] === null
            || (int)$task['assignee_id'] === (int)$user['id'];
    }

    public static function approve(array $user): bool { return self::admin($user); }
    private static function admin(array $user): bool { return ($user['role'] ?? '') === 'admin'; }
}
