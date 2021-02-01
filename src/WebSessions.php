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
        // retrieve session data
        $sessionId = $this->assignSessionData($request);

        /** @var Response $response */
        $response = $next($request);

        // if there's not already a session id then store it
        if ( ! $sessionId) {
            $sessionId = (string) Uuid::uuid4();
            $response = $response->withCookie(new Cookie('session_id', $sessionId));
        }

        // store session data
        $this->sessionStorage->store('session_data_' . $sessionId, $this->sessionData);

        // pass the response on
        return $response;
    }

    private function assignSessionData(Request $request)
    {
        $currentSessionId = $request->cookies()->get('session_id');
        if ($currentSessionId) {
            $this->sessionData->overwrite(
                $this->sessionStorage->retrieve('session_data_' . $currentSessionId)
            );
        }
        return $currentSessionId;
    }
}