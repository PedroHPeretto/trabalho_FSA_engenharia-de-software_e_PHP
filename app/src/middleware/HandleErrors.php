<?php

namespace App\Middleware;

use App\Exceptions\BookAvailableException;
use App\Exceptions\OutOfStockException;
use App\Exceptions\PendingFineException;
use App\Exceptions\RenewalBlockedException;
use App\Exceptions\ReservationNotFoundException;
use App\Exceptions\UserBlockedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleErrors
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (UserBlockedException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (PendingFineException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (OutOfStockException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (BookAvailableException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (ReservationNotFoundException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (RenewalBlockedException $e) {
            return $this->redirectBack($request, $e->getMessage());
        } catch (\RuntimeException $e) {
            return $this->redirectBack($request, $e->getMessage());
        }
    }

    private function redirectBack(Request $request, string $message): Response
    {
        $previous = $request->headers->get('referer')
            ?? ($request->hasSession() ? $request->session()->previousUrl() : null)
            ?? '/';

        return redirect($previous)
            ->withErrors(['error' => $message])
            ->withInput();
    }
}
