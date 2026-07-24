<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization\Member;

enum MemberRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public static function fromName(string $roleName): self
    {
        return match ($roleName) {
            'admin' => self::ADMIN,
            'member' => self::MEMBER,
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
