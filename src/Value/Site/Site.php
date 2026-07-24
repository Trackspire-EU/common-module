<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Site;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\Organization\OrganizationId;
use Trackspire\CommonModule\Value\User\UserId;
use Trackspire\CommonModule\Value\UtcDate;

class Site
{
    private function __construct(
        private readonly ?SiteId $siteId,
        private readonly OrganizationId $organizationId,
        private readonly string $name,
        private readonly SiteDomain $domain,
        private readonly UserId $createdBy,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
        private readonly SiteTrackingConfig $trackingConfig,
        private readonly SiteFilters $filters,
    ) {
    }

    public static function create(
        OrganizationId $organizationId,
        UserId $createdBy,
        string $name,
        SiteDomain $domain,
    ): self {
        return new self(
            siteId:         null,
            organizationId: $organizationId,
            name:           $name,
            domain:         $domain,
            createdBy:      $createdBy,
            createdAt:      UtcDate::now(),
            updatedAt:      null,
            trackingConfig: SiteTrackingConfig::defaults(),
            filters:        SiteFilters::defaults(),
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            siteId:         SiteId::from((int) $row['site_id']),
            organizationId: OrganizationId::from($row['organization_id']),
            name:           $row['name'],
            domain:         SiteDomain::from($row['domain']),
            createdBy:      UserId::from($row['created_by']),
            createdAt:      UtcDate::from('Y-m-d H:i:s', $row['created_at']),
            updatedAt:      isset($row['updated_at']) ? UtcDate::from('Y-m-d H:i:s', $row['updated_at']) : null,
            trackingConfig: SiteTrackingConfig::fromDatabase($row),
            filters:        SiteFilters::fromDatabase($row),
        );
    }

    public function toArray(): array
    {
        return [
            'siteId'         => $this->siteId?->asInt(),
            'organizationId' => $this->organizationId->asString(),
            'name'           => $this->name,
            'domain'    => $this->domain->asString(),
            'createdBy' => $this->createdBy->asString(),
            'createdAt' => $this->createdAt->format(DATE_ATOM),
            'updatedAt' => $this->updatedAt?->format(DATE_ATOM),
            ...$this->trackingConfig->toArray(),
            ...$this->filters->toArray(),
        ];
    }

    public function getSiteId(): ?SiteId
    {
        return $this->siteId;
    }

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomain(): SiteDomain
    {
        return $this->domain;
    }

    public function getCreatedBy(): UserId
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTrackingConfig(): SiteTrackingConfig
    {
        return $this->trackingConfig;
    }

    public function getFilters(): SiteFilters
    {
        return $this->filters;
    }
}
