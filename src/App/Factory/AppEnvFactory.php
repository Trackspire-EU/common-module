<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\App\Factory;

use Psr\Container\ContainerInterface;
use Trackspire\CommonModule\Repository\EnvironmentRepository;
use Trackspire\CommonModule\Value\Common\AppEnv;

class AppEnvFactory
{
    public function __invoke(ContainerInterface $container): AppEnv
    {
        $environmentRepository = $container->get(EnvironmentRepository::class);
        $value = $environmentRepository->getEnvironmentVariable('APP_ENV');

        return AppEnv::create($value);
    }
}
