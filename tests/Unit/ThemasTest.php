<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Themas;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class ThemasTest extends TestCase
{
    private function validThemaData(): array
    {
        return [
            'naam'             => 'Wonen & Leven',
            'producttype_uuids' => [],
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Wonen']], 200)]);

        $result = Themas::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/themas'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Themas::list(['naam' => 'Wonen']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'naam=Wonen'));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440010';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Wonen'], 200)]);

        $result = Themas::get($uuid);

        $this->assertIsArray($result);
        $this->assertSame($uuid, $result['uuid']);
        Http::assertSent(fn($req) => str_contains($req->url(), 'themas/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Wonen & Leven'], 201)]);

        $result = Themas::create($this->validThemaData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/themas'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validThemaData();
        unset($data['naam']);

        Themas::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440010';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Themas::update($uuid, $this->validThemaData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'themas/' . $uuid));
    }

    public function test_update_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validThemaData();
        unset($data['naam']);

        Themas::update('some-uuid', $data);
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440010';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Patched'], 200)]);

        $result = Themas::patch($uuid, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'themas/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440010';
        Http::fake(['*' => Http::response('', 204)]);

        Themas::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'themas/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Themas::get('non-existent-uuid');
    }
}
