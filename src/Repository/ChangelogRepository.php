<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\Changelog\ChangelogCategory;
use Trackspire\CommonModule\Value\Changelog\ChangelogEntries;
use Trackspire\CommonModule\Value\Changelog\ChangelogEntry;
use Trackspire\CommonModule\Value\Changelog\ChangelogEntryId;
use Trackspire\CommonModule\Value\Changelog\ChangelogSource;

class ChangelogRepository
{
    private const string COLUMNS =
        'id, version, category, title, body, source, github_release_id, published_at, created_at, updated_at';

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function loadAll(): ChangelogEntries
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM changelog_entries ORDER BY published_at DESC';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return ChangelogEntries::fromDatabaseRows($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load changelog entries',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function loadPublished(): ChangelogEntries
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM changelog_entries
                WHERE published_at <= NOW() ORDER BY published_at DESC';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return ChangelogEntries::fromDatabaseRows($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load published changelog entries',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function loadById(ChangelogEntryId $id): ?ChangelogEntry
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM changelog_entries WHERE id = :id';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id->asString()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? ChangelogEntry::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load changelog entry',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function findByGithubReleaseId(string $githubReleaseId): ?ChangelogEntry
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM changelog_entries WHERE github_release_id = :githubReleaseId';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['githubReleaseId' => $githubReleaseId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row !== false ? ChangelogEntry::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load changelog entry by github release id',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function create(
        string $version,
        ChangelogCategory $category,
        string $title,
        string $body,
        ChangelogSource $source,
        ?string $githubReleaseId,
        string $publishedAt,
    ): ChangelogEntryId {
        $id = ChangelogEntryId::fromRandom();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO changelog_entries
                    (id, version, category, title, body, source, github_release_id, published_at)
                 VALUES
                    (:id, :version, :category, :title, :body, :source, :githubReleaseId, :publishedAt)'
            );
            $stmt->execute([
                'id' => $id->asString(),
                'version' => $version,
                'category' => $category->value,
                'title' => $title,
                'body' => $body,
                'source' => $source->value,
                'githubReleaseId' => $githubReleaseId,
                'publishedAt' => $publishedAt,
            ]);
            return $id;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to create changelog entry',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function update(
        ChangelogEntryId $id,
        string $version,
        ChangelogCategory $category,
        string $title,
        string $body,
        string $publishedAt,
    ): void {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE changelog_entries
                 SET version = :version, category = :category, title = :title,
                     body = :body, published_at = :publishedAt
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $id->asString(),
                'version' => $version,
                'category' => $category->value,
                'title' => $title,
                'body' => $body,
                'publishedAt' => $publishedAt,
            ]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to update changelog entry',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function delete(ChangelogEntryId $id): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM changelog_entries WHERE id = :id');
            $stmt->execute(['id' => $id->asString()]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to delete changelog entry',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }
}
