<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Organization\Invitation;

use DateInterval;
use DateTimeImmutable;
use Trackspire\CommonModule\Value\Organization\Member\MemberRole;
use Trackspire\CommonModule\Value\Organization\OrganizationId;
use Trackspire\CommonModule\Value\User\UserEmail;
use Trackspire\CommonModule\Value\User\UserId;

class Invitation
{
    private function __construct(
        private readonly string $invitationId,
        private readonly OrganizationId $organizationId,
        private readonly UserId $invitedBy,
        private readonly UserEmail $email,
        private readonly MemberRole $role,
        private readonly string $token,
        private readonly DateTimeImmutable $expiresAt,
    ) {
    }

    public static function create(
        OrganizationId $organizationId,
        UserId $invitedBy,
        UserEmail $email,
        MemberRole $role,
    ): self {
        $invitationId = bin2hex(random_bytes(16));
        $token = bin2hex(random_bytes(32));
        $expiresAt = new DateTimeImmutable()->add(new DateInterval('P7D'));

        return new self(
            $invitationId,
            $organizationId,
            $invitedBy,
            $email,
            $role,
            $token,
            $expiresAt
        );
    }

    public static function from(
        string $invitationId,
        OrganizationId $organizationId,
        UserId $invitedBy,
        UserEmail $email,
        MemberRole $role,
        string $token,
        DateTimeImmutable $expiresAt,
    ): self {
        return new self(
            $invitationId,
            $organizationId,
            $invitedBy,
            $email,
            $role,
            $token,
            $expiresAt
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            $row['id'],
            OrganizationId::from($row['organization_id']),
            UserId::from($row['invited_by_email']),
            UserEmail::from($row['email']),
            MemberRole::from($row['role']),
            $row['token'],
            new DateTimeImmutable($row['expires_at'])
        );
    }

    public function toArray(): array
    {
        return [
            'invitationId' => $this->invitationId,
            'organizationId' => $this->organizationId->asString(),
            'invitedBy' => $this->invitedBy->asString(),
            'email' => $this->email->asString(),
            'role' => $this->role->getValue(),
            'token' => $this->token,
            'expiresAt' => $this->expiresAt->format('Y-m-d H:i:s'),
        ];
    }

    public function getInvitationId(): string
    {
        return $this->invitationId;
    }

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function getInvitedBy(): UserId
    {
        return $this->invitedBy;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getRole(): MemberRole
    {
        return $this->role;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }
}