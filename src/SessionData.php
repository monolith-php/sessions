<?php namespace Monolith\WebSessions;

use JetBrains\PhpStorm\Pure;
use Monolith\Collections\MutableDictionary;

final class SessionData
{
    private MutableDictionary $data;

    public function __construct(MutableDictionary $data = null)
    {
        $this->data = $data ?: new MutableDictionary;
    }

    public static function fromArray(array $data): static
    {
        return new static(new MutableDictionary($data));
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

    public function all(): MutableDictionary
    {
        return $this->data;
    }

    #[Pure] public function toArray(): array
    {
        return $this->data->toArray();
    }
}