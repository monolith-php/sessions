<?php namespace Monolith\WebSessions;

use Monolith\Configuration\Config;
use Monolith\ComponentBootstrapping\ComponentBootstrap;
use Monolith\DependencyInjection\Container;

final class WebSessionsBootstrap implements ComponentBootstrap
{
    /** @var Config */
    private $config;

    public function bind(Container $container): void
    {
        $this->config = $container(Config::class);

        $container->singleton(WebSessionStorage::class, function ($r) {
            return new RedisWebSessionStorage(
                $r(\Predis\Client::class)
            );
        });

        $container->singleton(\Predis\Client::class, function($r) {
            return new \Predis\Client([
                'scheme' => $this->config->get('WEB_SESSIONS_REDIS_SCHEME'),
                'host'   => $this->config->get('WEB_SESSIONS_REDIS_HOST'),
                'port'   => $this->config->get('WEB_SESSIONS_REDIS_PORT'),
            ]);
        });

        $container->singleton(SessionData::class);
    }

    public function init(Container $container): void
    {
    }
}