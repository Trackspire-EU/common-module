<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Exception;

use Stringable;

interface FormattableException
{
    public function format(): string|Stringable;

    public function formatHuman(): string|Stringable;
}
