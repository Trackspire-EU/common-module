<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Subscription;

use DateTimeImmutable;
use Trackspire\CommonModule\Value\AbstractId;
use Trackspire\CommonModule\Value\Organization\OrganizationId;
use Trackspire\CommonModule\Value\UtcDate;

class Subscription
{
    private function __construct(
        private readonly string $id,
        private readonly OrganizationId $organizationId,
        private SubscriptionPlan $plan,
        private SubscriptionStatus $status,
        private ?string $stripeCustomerId,
        private ?string $stripeSubscriptionId,
        private ?string $stripePriceId,
        private ?DateTimeImmutable $trialEndsAt,
        private ?DateTimeImmutable $currentPeriodStart,
        private ?DateTimeImmutable $currentPeriodEnd,
        private ?DateTimeImmutable $canceledAt,
    ) {
    }

    public static function createFree(OrganizationId $organizationId): self
    {
        return new self(
            id: AbstractId::fromRandom()->asString(),
            organizationId: $organizationId,
            plan: SubscriptionPlan::FREE,
            status: SubscriptionStatus::ACTIVE,
            stripeCustomerId: null,
            stripeSubscriptionId: null,
            stripePriceId: null,
            trialEndsAt: null,
            currentPeriodStart: null,
            currentPeriodEnd: null,
            canceledAt: null,
        );
    }

    public static function fromDatabase(array $row): self
    {
        return new self(
            id: $row['id'],
            organizationId: OrganizationId::from($row['organization_id']),
            plan: SubscriptionPlan::from($row['plan']),
            status: SubscriptionStatus::from($row['status']),
            stripeCustomerId: $row['stripe_customer_id'] ?? null,
            stripeSubscriptionId: $row['stripe_subscription_id'] ?? null,
            stripePriceId: $row['stripe_price_id'] ?? null,
            trialEndsAt: isset($row['trial_ends_at'])
                ? UtcDate::from(UtcDate::FORMAT_HUMAN, $row['trial_ends_at'])
                : null,
            currentPeriodStart: isset($row['current_period_start'])
                ? UtcDate::from(UtcDate::FORMAT_HUMAN, $row['current_period_start'])
                : null,
            currentPeriodEnd: isset($row['current_period_end'])
                ? UtcDate::from(UtcDate::FORMAT_HUMAN, $row['current_period_end'])
                : null,
            canceledAt: isset($row['canceled_at'])
                ? UtcDate::from(UtcDate::FORMAT_HUMAN, $row['canceled_at'])
                : null,
        );
    }

    public function isActive(): bool
    {
        return in_array($this->status, [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIALING], true);
    }

    public function isPro(): bool
    {
        if ($this->plan !== SubscriptionPlan::PRO) {
            return false;
        }
        // CANCELED with PRO plan = cancel_at_period_end, access remains until period end
        return in_array($this->status, [
            SubscriptionStatus::ACTIVE,
            SubscriptionStatus::TRIALING,
            SubscriptionStatus::CANCELED,
        ], true);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function getPlan(): SubscriptionPlan
    {
        return $this->plan;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function getTrialEndsAt(): ?DateTimeImmutable
    {
        return $this->trialEndsAt;
    }

    public function getCurrentPeriodStart(): ?DateTimeImmutable
    {
        return $this->currentPeriodStart;
    }

    public function getCurrentPeriodEnd(): ?DateTimeImmutable
    {
        return $this->currentPeriodEnd;
    }

    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function withStripeCustomerId(string $customerId): self
    {
        $clone = clone $this;
        $clone->stripeCustomerId = $customerId;
        return $clone;
    }

    public function withStripeSubscription(
        string $subscriptionId,
        string $priceId,
        SubscriptionPlan $plan,
        SubscriptionStatus $status,
        ?DateTimeImmutable $trialEndsAt,
        ?DateTimeImmutable $periodStart,
        ?DateTimeImmutable $periodEnd,
    ): self {
        $clone = clone $this;
        $clone->stripeSubscriptionId = $subscriptionId;
        $clone->stripePriceId = $priceId;
        $clone->plan = $plan;
        $clone->status = $status;
        $clone->trialEndsAt = $trialEndsAt;
        $clone->currentPeriodStart = $periodStart;
        $clone->currentPeriodEnd = $periodEnd;
        return $clone;
    }

    public function withStatus(SubscriptionStatus $status, ?DateTimeImmutable $canceledAt = null): self
    {
        $clone = clone $this;
        $clone->status = $status;
        $clone->canceledAt = $canceledAt;
        return $clone;
    }

    public function withPlan(SubscriptionPlan $plan): self
    {
        $clone = clone $this;
        $clone->plan = $plan;
        return $clone;
    }

    public function __serialize(): array
    {
        return [
            'id'                     => $this->id,
            'organizationId'         => $this->organizationId->asString(),
            'plan'                   => $this->plan->value,
            'status'                 => $this->status->value,
            'stripeCustomerId'       => $this->stripeCustomerId,
            'stripeSubscriptionId'   => $this->stripeSubscriptionId,
            'stripePriceId'          => $this->stripePriceId,
            'trialEndsAt'            => $this->trialEndsAt?->format('Y-m-d H:i:s'),
            'currentPeriodStart'     => $this->currentPeriodStart?->format('Y-m-d H:i:s'),
            'currentPeriodEnd'       => $this->currentPeriodEnd?->format('Y-m-d H:i:s'),
            'canceledAt'             => $this->canceledAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id                   = $data['id'];
        $this->organizationId       = OrganizationId::from($data['organizationId']);
        $this->plan                 = SubscriptionPlan::from($data['plan']);
        $this->status               = SubscriptionStatus::from($data['status']);
        $this->stripeCustomerId     = $data['stripeCustomerId'];
        $this->stripeSubscriptionId = $data['stripeSubscriptionId'];
        $this->stripePriceId        = $data['stripePriceId'];
        $this->trialEndsAt          = isset($data['trialEndsAt'])
            ? new DateTimeImmutable($data['trialEndsAt'])
            : null;
        $this->currentPeriodStart   = isset($data['currentPeriodStart'])
            ? new DateTimeImmutable($data['currentPeriodStart'])
            : null;
        $this->currentPeriodEnd     = isset($data['currentPeriodEnd'])
            ? new DateTimeImmutable($data['currentPeriodEnd'])
            : null;
        $this->canceledAt           = isset($data['canceledAt'])
            ? new DateTimeImmutable($data['canceledAt'])
            : null;
    }

    public function toArray(): array
    {
        return [
            'plan'             => $this->plan->value,
            'status'           => $this->status->value,
            'trialEndsAt'      => $this->trialEndsAt?->format(DATE_ATOM),
            'currentPeriodEnd' => $this->currentPeriodEnd?->format(DATE_ATOM),
            'canceledAt'       => $this->canceledAt?->format(DATE_ATOM),
        ];
    }
}
