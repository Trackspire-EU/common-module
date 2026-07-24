<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\User\UserId;
use Trackspire\CommonModule\Value\UtcDate;

class Organization
{
    private function __construct(
        private readonly OrganizationId $organizationId,
        private readonly string $name,
        private readonly string $slug,
        private readonly UserId $createdBy,
        private readonly DateTimeImmutable $createdAt
    ) {
    }

    public static function create(UserId $createdBy, string $name, string $slug): self
    {
        return new self(
            OrganizationId::fromRandom(),
            $name,
            $slug,
            $createdBy,
            UtcDate::now(),
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            OrganizationId::from($row['organization_id']),
            $row['name'],
            $row['slug'],
            UserId::from($row['created_by']),
            UtcDate::from('Y-m-d H:i:s', $row['created_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'organizationId' => $this->organizationId->asString(),
            'name' => $this->name,
            'slug' => $this->slug,
            'createdBy' => $this->createdBy->asString(),
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedBy(): UserId
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
