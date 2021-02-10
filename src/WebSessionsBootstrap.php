<?php namespace Monolith\WebSessions;

use Monolith\Configuration\Config;
use Monolith\DependencyInjection\Container;
use Monolith\ComponentBootstrapping\ComponentBootstrap;

final class WebSessionsBootstrap implements ComponentBootstrap
{
    private Config $config;

    public function bind(Container $container): void
    {
        # same data object everywhere that we load it
        # this starts empty, but will be managed by
        # the WebSessions middleware
        $container->singleton(SessionData::class, new SessionData());

        $this->config = $container(Config::class);

        $container->singleton(
            WebSessionStorage::class,
            function ($r) {
                return new RedisWebSessionStorage(
                    $r(\Predis\Client::class)
                );
            }
        );

        $container->singleton(
            \Predis\Client::class,
            function ($r) {
                return new \Predis\Client(
                    [
                        'scheme' => $this->config->get('WEB_SESSIONS_REDIS_SCHEME'),
                        'host' => $this->config->get('WEB_SESSIONS_REDIS_HOST'),
                        'port' => $this->config->get('WEB_SESSIONS_REDIS_PORT'),
                    ]
                );
            }
        );
    }

    public function init(Container $container): void
    {
    }
}