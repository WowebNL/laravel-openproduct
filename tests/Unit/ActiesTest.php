<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Acties;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class ActiesTest extends TestCase
{
    private function validActieData(): array
    {
        return [
            'naam'             => 'Indienen aanvraag',
            'tabel_endpoint'   => 'https://example.com/beslistabellen/pt-001',
            'dmn_tabel_id'     => 'pt-001-aanvraag',
            'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440060',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Actie A']], 200)]);

        $result = Acties::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/acties'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Acties::list(['producttype_uuid' => '550e8400-e29b-41d4-a716-446655440060']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'producttype_uuid='));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440060';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Actie A'], 200)]);

        $result = Acties::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'acties/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Indienen aanvraag'], 201)]);

        $result = Acties::create($this->validActieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/acties'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validActieData();
        unset($data['naam']);

        Acties::create($data);
    }

    public function test_create_throws_validation_exception_for_invalid_tabel_endpoint(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validActieData();
        $data['tabel_endpoint'] = 'not-a-url';

        Acties::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validActieData();
        unset($data['producttype_uuid']);

        Acties::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440060';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Acties::update($uuid, $this->validActieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'acties/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440060';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Patched'], 200)]);

        $result = Acties::patch($uuid, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'acties/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440060';
        Http::fake(['*' => Http::response('', 204)]);

        Acties::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'acties/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Acties::get('non-existent-uuid');
    }
}
