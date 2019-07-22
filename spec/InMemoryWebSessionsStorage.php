<?php namespace spec\Monolith\WebSessions;

use Monolith\Collections\Dictionary;
use Monolith\WebSessions\SessionData;
use Monolith\WebSessions\WebSessionStorage;

final class InMemoryWebSessionsStorage implements WebSessionStorage
{
    private $sessions;

    public function __construct()
    {
        // map of session id => SessionData
        $this->sessions = Dictionary::empty();
    }

    public function store(string $key, SessionData $data)
    {
        $this->sessions->add($key, $data);
    }

    public function retrieve(string $key): SessionData
    {
        return $this->sessions->get($key);
    }
}