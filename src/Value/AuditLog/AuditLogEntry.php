<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\AuditLog;

use DateTimeImmutable;

class AuditLogEntry
{
    private function __construct(
        private readonly AuditLogEntryId $id,
        private readonly string $actorId,
        private readonly ?string $actorEmail,
        private readonly string $actionType,
        private readonly string $entityType,
        private readonly string $entityId,
        private readonly ?array $metadata,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            AuditLogEntryId::from($row['id']),
            $row['actor_id'],
            $row['actor_email'] ?? null,
            $row['action_type'],
            $row['entity_type'],
            $row['entity_id'],
            isset($row['metadata']) ? json_decode($row['metadata'], true) : null,
            new DateTimeImmutable($row['created_at']),
        );
    }

    public function getId(): AuditLogEntryId
    {
        return $this->id;
    }

    public function getActorId(): string
    {
        return $this->actorId;
    }

    public function getActorEmail(): ?string
    {
        return $this->actorEmail;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->asString(),
            'actor_id' => $this->actorId,
            'actor_email' => $this->actorEmail,
            'action_type' => $this->actionType,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
