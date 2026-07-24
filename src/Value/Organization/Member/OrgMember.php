<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization\Member;

use Trackspire\CommonModule\Value\Organization\OrganizationId;
use Trackspire\CommonModule\Value\User\UserId;

class OrgMember
{
    public function __construct(
        private readonly OrganizationId $organizationId,
        private readonly UserId $userId,
        private readonly MemberRole $role,
    ) {
    }

    public static function from(
        OrganizationId $organizationId,
        UserId $userId,
        MemberRole $role
    ): self {
        return new self($organizationId, $userId, $role);
    }

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getRole(): MemberRole
    {
        return $this->role;
    }
}
