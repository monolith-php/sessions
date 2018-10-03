<?php namespace Monolith\WebSessions;

use Monolith\Collections\Map;

final class SessionData
{
    /** @var Map */
    private $data;

    public function __construct(Map $data = null)
    {
        $this->data = $data ?: new Map;
    }

    public static function fromArray(array $data)
    {
        return new static(new Map($data));
    }

    public function has(string $key): bool
    {
        return $this->data->has($key);
    }

    public function get(string $key)
    {
        return $this->data->get($key);
    }

    public function set(string $key, $value)
    {
        $this->data->add($key, $value);
    }

    public function remove(string $key)
    {
        $this->data->remove($key);
    }

    public function overwrite(SessionData $newData)
    {
        $this->data = $newData->all();
    }

    public function all(): Map
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->data->toArray();
    }
}