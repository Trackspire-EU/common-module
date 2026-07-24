<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Subscription;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case PRO  = 'pro';
}
