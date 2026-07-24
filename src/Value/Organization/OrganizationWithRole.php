<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization;

use Trackspire\CommonModule\Value\Organization\Member\MemberRole;

class OrganizationWithRole
{
    private function __construct(
        private readonly Organization $organization,
        private readonly MemberRole $role,
    ) {
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            Organization::fromDatabase($row),
            MemberRole::fromName($row['role']),
        );
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getRole(): MemberRole
    {
        return $this->role;
    }

    public function toArray(): array
    {
        return array_merge($this->organization->toArray(), [
            'role' => $this->role->getValue(),
        ]);
    }
}
