<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Links;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class LinksTest extends TestCase
{
    private function validLinkData(): array
    {
        return [
            'naam'             => 'Meer informatie',
            'url'              => 'https://example.com/meer-informatie',
            'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440040',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Link A']], 200)]);

        $result = Links::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/links'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Links::list(['producttype_uuid' => '550e8400-e29b-41d4-a716-446655440040']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'producttype_uuid='));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440040';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Link A'], 200)]);

        $result = Links::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'links/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Meer informatie'], 201)]);

        $result = Links::create($this->validLinkData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/links'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validLinkData();
        unset($data['naam']);

        Links::create($data);
    }

    public function test_create_throws_validation_exception_for_invalid_url(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validLinkData();
        $data['url'] = 'not-a-valid-url';

        Links::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validLinkData();
        unset($data['producttype_uuid']);

        Links::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440040';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Links::update($uuid, $this->validLinkData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'links/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440040';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Patched'], 200)]);

        $result = Links::patch($uuid, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'links/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440040';
        Http::fake(['*' => Http::response('', 204)]);

        Links::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'links/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Links::get('non-existent-uuid');
    }
}
