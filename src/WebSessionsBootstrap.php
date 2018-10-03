<?php namespace Monolith\WebSessions;

use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;

final class WebSessionsBootstrap implements ComponentBootstrap
{
    public function bind(Container $container): void
    {
        $container->singleton(WebSessionStorage::class, function ($r) {
            return new RedisWebSessionStorage(
                $r(\Predis\Client::class)
            );
        });

        $container->singleton(\Predis\Client::class, function($r) {
            return new \Predis\Client([
                'scheme' => getenv('WEB_SESSIONS_REDIS_SCHEME'),
                'host'   => getenv('WEB_SESSIONS_REDIS_HOST'),
                'port'   => getenv('WEB_SESSIONS_REDIS_PORT'),
            ]);
        });

        $container->singleton(SessionData::class);
    }

    public function init(Container $container): void
    {
    }
}