<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Schemas;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class SchemasTest extends TestCase
{
    private function validSchemaData(): array
    {
        return [
            'naam'   => 'Aanvraagschema',
            'schema' => ['type' => 'object', 'properties' => []],
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['id' => 1, 'naam' => 'Schema A']], 200)]);

        $result = Schemas::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/schemas'));
    }

    public function test_get_returns_array(): void
    {
        Http::fake(['*' => Http::response(['id' => 42, 'naam' => 'Schema A'], 200)]);

        $result = Schemas::get(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
        Http::assertSent(fn($req) => str_contains($req->url(), 'schemas/42'));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['id' => 1, 'naam' => 'Aanvraagschema'], 201)]);

        $result = Schemas::create($this->validSchemaData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/schemas'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validSchemaData();
        unset($data['naam']);

        Schemas::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_schema(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validSchemaData();
        unset($data['schema']);

        Schemas::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['id' => 42, 'naam' => 'Updated'], 200)]);

        $result = Schemas::update(42, $this->validSchemaData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'schemas/42'));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['id' => 42, 'naam' => 'Patched'], 200)]);

        $result = Schemas::patch(42, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'schemas/42'));
    }

    public function test_delete_sends_delete_request(): void
    {
        Http::fake(['*' => Http::response('', 204)]);

        Schemas::delete(42);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'schemas/42'));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Schemas::get(999);
    }
}
