<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Changelog;

use DateTimeImmutable;

class ChangelogEntry
{
    private function __construct(
        private readonly ChangelogEntryId $id,
        private readonly string $version,
        private readonly ChangelogCategory $category,
        private readonly string $title,
        private readonly string $body,
        private readonly ChangelogSource $source,
        private readonly ?string $githubReleaseId,
        private readonly DateTimeImmutable $publishedAt,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            ChangelogEntryId::from($row['id']),
            $row['version'],
            ChangelogCategory::fromString($row['category']),
            $row['title'],
            $row['body'],
            ChangelogSource::from($row['source']),
            $row['github_release_id'],
            new DateTimeImmutable($row['published_at']),
            new DateTimeImmutable($row['created_at']),
            isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null,
        );
    }

    public function getId(): ChangelogEntryId
    {
        return $this->id;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->asString(),
            'version' => $this->version,
            'category' => $this->category->value,
            'title' => $this->title,
            'body' => $this->body,
            'source' => $this->source->value,
            'github_release_id' => $this->githubReleaseId,
            'published_at' => $this->publishedAt->format(DATE_ATOM),
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
