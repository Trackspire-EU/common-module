<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Subscription;

enum SubscriptionStatus: string
{
    case ACTIVE     = 'active';
    case TRIALING   = 'trialing';
    case PAST_DUE   = 'past_due';
    case CANCELED   = 'canceled';
    case INCOMPLETE = 'incomplete';
}
