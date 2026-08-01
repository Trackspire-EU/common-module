<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use Trackspire\CommonModule\Exception\DatabaseException;
use Trackspire\CommonModule\Value\Organization\OrganizationId;
use Trackspire\CommonModule\Value\Subscription\Subscription;
use Trackspire\CommonModule\Value\UtcDate;

class SubscriptionRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function loadByOrganizationId(OrganizationId $organizationId): ?Subscription
    {
        $sql = <<<SQL
            SELECT id, organization_id, plan, status, stripe_customer_id, stripe_subscription_id,
                   stripe_price_id, trial_ends_at, current_period_start, current_period_end, canceled_at
            FROM subscriptions
            WHERE organization_id = :organizationId
            LIMIT 1
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['organizationId' => $organizationId->asString()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load subscription',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }

        return Subscription::fromDatabase($row);
    }

    public function loadByStripeSubscriptionId(string $stripeSubscriptionId): ?Subscription
    {
        $sql = <<<SQL
            SELECT id, organization_id, plan, status, stripe_customer_id, stripe_subscription_id,
                   stripe_price_id, trial_ends_at, current_period_start, current_period_end, canceled_at
            FROM subscriptions
            WHERE stripe_subscription_id = :stripeSubscriptionId
            LIMIT 1
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['stripeSubscriptionId' => $stripeSubscriptionId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load subscription by Stripe subscription ID',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }

        return Subscription::fromDatabase($row);
    }

    public function loadByStripeCustomerId(string $stripeCustomerId): ?Subscription
    {
        $sql = <<<SQL
            SELECT id, organization_id, plan, status, stripe_customer_id, stripe_subscription_id,
                   stripe_price_id, trial_ends_at, current_period_start, current_period_end, canceled_at
            FROM subscriptions
            WHERE stripe_customer_id = :stripeCustomerId
            LIMIT 1
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['stripeCustomerId' => $stripeCustomerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to load subscription by Stripe customer ID',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }

        return Subscription::fromDatabase($row);
    }

    public function save(Subscription $subscription): void
    {
        $sql = <<<SQL
            INSERT INTO subscriptions (
                id, organization_id, plan, status,
                stripe_customer_id, stripe_subscription_id, stripe_price_id,
                trial_ends_at, current_period_start, current_period_end, canceled_at
            ) VALUES (
                :id, :organizationId, :plan, :status,
                :stripeCustomerId, :stripeSubscriptionId, :stripePriceId,
                :trialEndsAt, :currentPeriodStart, :currentPeriodEnd, :canceledAt
            )
            ON DUPLICATE KEY UPDATE
                plan                   = VALUES(plan),
                status                 = VALUES(status),
                stripe_customer_id     = VALUES(stripe_customer_id),
                stripe_subscription_id = VALUES(stripe_subscription_id),
                stripe_price_id        = VALUES(stripe_price_id),
                trial_ends_at          = VALUES(trial_ends_at),
                current_period_start   = VALUES(current_period_start),
                current_period_end     = VALUES(current_period_end),
                canceled_at            = VALUES(canceled_at)
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id'                   => $subscription->getId(),
                'organizationId'       => $subscription->getOrganizationId()->asString(),
                'plan'                 => $subscription->getPlan()->value,
                'status'               => $subscription->getStatus()->value,
                'stripeCustomerId'     => $subscription->getStripeCustomerId(),
                'stripeSubscriptionId' => $subscription->getStripeSubscriptionId(),
                'stripePriceId'        => $subscription->getStripePriceId(),
                'trialEndsAt'          => $subscription->getTrialEndsAt()?->format(UtcDate::FORMAT_HUMAN),
                'currentPeriodStart'   => $subscription->getCurrentPeriodStart()?->format(UtcDate::FORMAT_HUMAN),
                'currentPeriodEnd'     => $subscription->getCurrentPeriodEnd()?->format(UtcDate::FORMAT_HUMAN),
                'canceledAt'           => $subscription->getCanceledAt()?->format(UtcDate::FORMAT_HUMAN),
            ]);
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Failed to save subscription',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}