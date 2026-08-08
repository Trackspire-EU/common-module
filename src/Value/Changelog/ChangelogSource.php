<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Value\Changelog;

enum ChangelogSource: string
{
    case Admin = 'admin';
    case Github = 'github';
}
