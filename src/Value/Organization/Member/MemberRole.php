<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization\Member;

enum MemberRole: string
{
    /** Exactly one per organization: may do everything an admin can, plus delete or hand over the organization */
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public static function fromName(string $roleName): self
    {
        return match ($roleName) {
            'owner' => self::OWNER,
            'admin' => self::ADMIN,
            'member' => self::MEMBER,
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /** True for the roles that manage the organization: its sites, settings, members and billing. */
    public function isAdministrative(): bool
    {
        return $this === self::OWNER || $this === self::ADMIN;
    }
}
