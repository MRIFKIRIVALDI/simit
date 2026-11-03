<?php
namespace Simit\Policies;

final class AttachmentPolicy
{
    public static function download(array $user, array $attachment): bool
    {
        return ($user['role'] ?? '') === 'admin'
            || $attachment['assignee_id'] === null
            || (int)$attachment['assignee_id'] === (int)$user['id']
            || (int)($attachment['created_by'] ?? 0) === (int)$user['id']
            || (int)($attachment['uploader_id'] ?? 0) === (int)$user['id']
            || (int)($attachment['is_contributor'] ?? 0) === 1;
    }
}
