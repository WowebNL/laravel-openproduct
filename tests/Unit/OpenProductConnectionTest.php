<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Tests\TestCase;

class OpenProductConnectionTest extends TestCase
{
    public function test_connection_sets_authorization_header(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        $connection = OpenProductConnection::getConnection();
        $connection->get('test');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Token test-token');
        });
    }

    public function test_connection_sets_base_url(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        OpenProductConnection::getConnection()->get('some-path');

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://open-product.test/');
        });
    }

    public function test_connection_sets_content_type_header(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        OpenProductConnection::getConnection()->get('test');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/json');
        });
    }
}
