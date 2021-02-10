<?php namespace Monolith\WebSessions;

use Ramsey\Uuid\Uuid;
use Monolith\Http\Cookie;
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
        $sessionId = $this->getSessionId($request);
        $storageKey = 'session_' . $sessionId;
        
        $this->sessionData->overwrite(
            $this->sessionStorage->retrieve($storageKey)
        );

        # Process Request
        /** @var Response $response */
        $response = $next($request);

        # Store Session
        $this->sessionStorage->store($storageKey, $this->sessionData);

        # Bubble Up, Buttercup
        return $response->withCookie(new Cookie('session_id', $sessionId));
    }
    
    protected function getSessionId(Request $request): string
    {
        return $request->cookies()->get('session_id') ?: Uuid::uuid4()->toString();
    }
}