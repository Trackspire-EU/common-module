<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\User\UserId;

class UserChangelogViewRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function loadLastSeenAt(UserId $userId): ?DateTimeImmutable
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT last_seen_at FROM user_changelog_views WHERE user_id = :userId'
            );
            $stmt->execute(['userId' => $userId->asString()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? new DateTimeImmutable($row['last_seen_at']) : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load changelog view state',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function markSeenNow(UserId $userId): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO user_changelog_views (user_id, last_seen_at)
                 VALUES (:userId, NOW())
                 ON DUPLICATE KEY UPDATE last_seen_at = NOW()'
            );
            $stmt->execute(['userId' => $userId->asString()]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to mark changelog as seen',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }
}
