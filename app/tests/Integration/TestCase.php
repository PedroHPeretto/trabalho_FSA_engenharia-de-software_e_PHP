<?php

namespace Tests\Integration;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable CSRF verification for all integration tests (Laravel 11 uses
        // ValidateCsrfToken in the web group, which extends VerifyCsrfToken).
        $this->withoutMiddleware([ValidateCsrfToken::class, VerifyCsrfToken::class]);
    }

    /**
     * Override to avoid calling url() before a request is bound in the container.
     * The base Laravel 11 implementation calls url($uri) which triggers UrlGenerator
     * resolution before any HTTP request is created, causing a TypeError when no
     * request is bound. We use config('app.url') directly instead.
     */
    protected function prepareUrlForRequest($uri): string
    {
        $uri = is_object($uri) && method_exists($uri, 'value') ? $uri->value() : (string) $uri;

        if (str_starts_with($uri, '/')) {
            $uri = substr($uri, 1);
        }

        return trim(config('app.url', 'http://localhost') . '/' . $uri, '/');
    }
}
