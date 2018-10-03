<?php namespace Monolith\WebSessions;

use Predis\Client;

final class RedisWebSessionStorage implements WebSessionStorage
{
    /** @var Client */
    private $predis;

    public function __construct(Client $predis)
    {
        $this->predis = $predis;
    }

    public function store(string $key, SessionData $values)
    {
        $this->predis->set($key, json_encode($values->toArray()));
    }

    public function retrieve(string $key): SessionData
    {
        $data = $this->predis->get($key);

        return $data ? SessionData::fromArray(json_decode($data, true)) : new SessionData;
    }
}