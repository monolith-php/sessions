<?php namespace Monolith\WebSessions;

interface WebSessionStorage
{
    public function store(string $key, SessionData $data);
    public function retrieve(string $key): SessionData;
}