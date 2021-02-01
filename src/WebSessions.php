<?php namespace Monolith\WebSessions;

use Ramsey\Uuid\Uuid;
use Monolith\Http\Request;
use Monolith\Http\Response;
use Monolith\WebRouting\Middleware;

final class WebSessions implements Middleware
{
    public function __construct(
        private WebSessionStorage $sessionStorage,
        private SessionData $sessionData
    ) {
    }

    public function process(Request $request, callable $next): Response
    {
        # Restore Session
        $sessionId =
            $request->cookies()->get('session_id')
                ?: Uuid::uuid4()->toString();

        $sessionKey = 'session_data_' . $sessionId;

        $this->sessionData->overwrite(
            $this->sessionStorage->retrieve($sessionKey)
        );

        # Process Request
        /** @var Response $response */
        $response = $next($request);

        # Store Session
        $this->sessionStorage->store($sessionKey, $this->sessionData);

        # Bubble Up, Buttercup
        return $response;
    }
}