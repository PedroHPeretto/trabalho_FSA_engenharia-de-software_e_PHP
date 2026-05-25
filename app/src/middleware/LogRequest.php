<?php

namespace App\Middleware;

use App\Services\LogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    public function __construct(
        private readonly LogService $logService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            auth()->check()
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])
            && $request->route()?->getName()
        ) {
            $this->logService->log(
                auth()->user(),
                strtolower($request->method()) . '.' . $request->route()->getName(),
                "HTTP {$request->method()} {$request->url()} → {$response->getStatusCode()}"
            );
        }

        return $response;
    }
}
