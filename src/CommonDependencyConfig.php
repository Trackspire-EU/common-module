<?php

declare(strict_types=1);

namespace Trackspire\CommonModule;

use DI\Definition\Source\DefinitionArray;
use Psr\Log\LoggerInterface;
use Trackspire\CommonModule\App\Factory\AppEnvFactory;
use Trackspire\CommonModule\App\Factory\LoggerFactory;
use Trackspire\CommonModule\Value\Common\AppEnv;

use function DI\factory;

class CommonDependencyConfig extends DefinitionArray
{
    public function __construct()
    {
        parent::__construct([
            LoggerInterface::class => factory(LoggerFactory::class),
            AppEnv::class => factory(AppEnvFactory::class),
        ]);
    }
}
