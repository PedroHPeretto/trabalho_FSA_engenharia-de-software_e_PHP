<?php

use App\Exceptions\BookAvailableException;
use App\Exceptions\OutOfStockException;
use App\Exceptions\PendingFineException;
use App\Exceptions\RenewalBlockedException;
use App\Exceptions\ReservationNotFoundException;
use App\Exceptions\UserBlockedException;
use App\Middleware\CheckRole;
use App\Middleware\LogRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../src/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'        => CheckRole::class,
            'log.request' => LogRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Redirect back with the error message for all domain exceptions that
        // should never surface as unhandled 500 errors in the browser.
        // The Routing\Pipeline renders exceptions through this handler, so
        // registering renderables here is the correct Laravel 11 intercept point.
        $errorRedirect = static function (\Throwable $e, Request $request) {
            return redirect($request->headers->get('referer', '/'))
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        };

        $exceptions->renderable(function (UserBlockedException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
        $exceptions->renderable(function (PendingFineException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
        $exceptions->renderable(function (OutOfStockException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
        $exceptions->renderable(function (BookAvailableException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
        $exceptions->renderable(function (ReservationNotFoundException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
        $exceptions->renderable(function (RenewalBlockedException $e, Request $request) use ($errorRedirect) {
            return $errorRedirect($e, $request);
        });
    })
    ->create();
