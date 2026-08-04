<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\Announcement\Announcement;
use Trackspire\CommonModule\Value\Announcement\AnnouncementId;
use Trackspire\CommonModule\Value\Announcement\AnnouncementType;
use Trackspire\CommonModule\Value\Announcement\Announcements;

class AnnouncementRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function loadAll(): Announcements
    {
        $sql = <<<SQL
            SELECT id, title, message, type, starts_at, ends_at, active, created_by, created_at, updated_at
            FROM announcements
            ORDER BY created_at DESC
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return Announcements::fromDatabaseRows($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load announcements',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function loadActive(): Announcements
    {
        $sql = <<<SQL
            SELECT id, title, message, type, starts_at, ends_at, active, created_by, created_at, updated_at
            FROM announcements
            WHERE active = 1
              AND (starts_at IS NULL OR starts_at <= NOW())
              AND (ends_at IS NULL OR ends_at >= NOW())
            ORDER BY created_at DESC
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return Announcements::fromDatabaseRows($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load active announcements',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function loadById(AnnouncementId $id): ?Announcement
    {
        $sql = <<<SQL
            SELECT id, title, message, type, starts_at, ends_at, active, created_by, created_at, updated_at
            FROM announcements
            WHERE id = :id
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id->asString()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? Announcement::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load announcement',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function create(
        string $title,
        string $message,
        AnnouncementType $type,
        ?string $startsAt,
        ?string $endsAt,
        bool $active,
        string $createdBy,
    ): AnnouncementId {
        $id = AnnouncementId::fromRandom();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO announcements (id, title, message, type, starts_at, ends_at, active, created_by)
                 VALUES (:id, :title, :message, :type, :startsAt, :endsAt, :active, :createdBy)'
            );
            $stmt->execute([
                'id' => $id->asString(),
                'title' => $title,
                'message' => $message,
                'type' => $type->value,
                'startsAt' => $startsAt,
                'endsAt' => $endsAt,
                'active' => $active ? 1 : 0,
                'createdBy' => $createdBy,
            ]);
            return $id;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to create announcement',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function update(
        AnnouncementId $id,
        string $title,
        string $message,
        AnnouncementType $type,
        ?string $startsAt,
        ?string $endsAt,
        bool $active,
    ): void {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE announcements
                 SET title = :title, message = :message, type = :type,
                     starts_at = :startsAt, ends_at = :endsAt, active = :active
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $id->asString(),
                'title' => $title,
                'message' => $message,
                'type' => $type->value,
                'startsAt' => $startsAt,
                'endsAt' => $endsAt,
                'active' => $active ? 1 : 0,
            ]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to update announcement',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function delete(AnnouncementId $id): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM announcements WHERE id = :id');
            $stmt->execute(['id' => $id->asString()]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to delete announcement',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
