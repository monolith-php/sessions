<?php namespace Monolith\WebSessions;

use Monolith\Collections\MutableMap;

final class SessionData
{
    /** @var MutableMap */
    private $data;

    public function __construct(MutableMap $data = null)
    {
        $this->data = $data ?: new MutableMap;
    }

    public static function fromArray(array $data)
    {
        return new static(new MutableMap($data));
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

    public function all(): MutableMap
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->data->toArray();
    }
}