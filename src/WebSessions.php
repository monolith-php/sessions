<?php namespace Monolith\WebSessions;

use Monolith\Http\Request;
use Monolith\Http\Response;
use Monolith\WebRouting\Middleware;

final class WebSessions implements Middleware
{
    public function process(Request $request, callable $next): Response
    {

    }
}