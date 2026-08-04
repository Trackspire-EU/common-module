<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Announcement;

use DateTimeImmutable;

class Announcement
{
    private function __construct(
        private readonly AnnouncementId $id,
        private readonly string $title,
        private readonly string $message,
        private readonly AnnouncementType $type,
        private readonly ?DateTimeImmutable $startsAt,
        private readonly ?DateTimeImmutable $endsAt,
        private readonly bool $active,
        private readonly string $createdBy,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            AnnouncementId::from($row['id']),
            $row['title'],
            $row['message'],
            AnnouncementType::fromString($row['type']),
            isset($row['starts_at']) ? new DateTimeImmutable($row['starts_at']) : null,
            isset($row['ends_at']) ? new DateTimeImmutable($row['ends_at']) : null,
            (bool) $row['active'],
            $row['created_by'],
            new DateTimeImmutable($row['created_at']),
            isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null,
        );
    }

    public function getId(): AnnouncementId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): AnnouncementType
    {
        return $this->type;
    }

    public function getStartsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function getEndsAt(): ?DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->asString(),
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type->value,
            'starts_at' => $this->startsAt?->format(DATE_ATOM),
            'ends_at' => $this->endsAt?->format(DATE_ATOM),
            'active' => $this->active,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
