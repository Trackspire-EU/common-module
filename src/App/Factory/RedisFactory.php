<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\App\Factory;

use Psr\Container\ContainerInterface;
use Redis;
use Trackspire\CommonModule\Repository\EnvironmentRepository;

class RedisFactory
{
    public function __invoke(ContainerInterface $container): Redis
    {
        /** @var EnvironmentRepository $envRepo */
        $envRepo = $container->get(EnvironmentRepository::class);
        $client = new Redis();

        $client->connect($envRepo->get('REDIS_HOST'), (int)$envRepo->get('REDIS_PORT'));

        $password = $envRepo->get('REDIS_PASSWORD');
        if (!empty($password)) {
            $client->auth($password);
        }

        return $client;
    }
}
