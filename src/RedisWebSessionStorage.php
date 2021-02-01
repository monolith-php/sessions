<?php namespace Monolith\WebSessions;

use Predis\Client;

final class RedisWebSessionStorage implements WebSessionStorage
{
    public function __construct(
        private Client $predis
    ) {
    }

    public function store(string $key, SessionData $data)
    {
        $this->predis->set(
            $key,
            json_encode($data->toArray())
        );
    }

    public function retrieve(string $key): SessionData
    {
        $data = $this->predis->get($key);

        return $data
            ? SessionData::fromArray(
                json_decode($data, true)
            )
            : new SessionData;
    }
}