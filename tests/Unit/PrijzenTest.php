<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Prijzen;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class PrijzenTest extends TestCase
{
    private function validPrijsData(): array
    {
        return [
            'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440030',
            'actief_vanaf'     => '2026-01-01',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc']], 200)]);

        $result = Prijzen::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/prijzen'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Prijzen::list(['producttype_uuid' => '550e8400-e29b-41d4-a716-446655440030']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'producttype_uuid='));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440030';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Prijzen::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'prijzen/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid'], 201)]);

        $result = Prijzen::create($this->validPrijsData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/prijzen'));
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validPrijsData();
        unset($data['producttype_uuid']);

        Prijzen::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_actief_vanaf(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validPrijsData();
        unset($data['actief_vanaf']);

        Prijzen::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440030';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Prijzen::update($uuid, $this->validPrijsData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'prijzen/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440030';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Prijzen::patch($uuid, ['actief_vanaf' => '2026-06-01']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'prijzen/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440030';
        Http::fake(['*' => Http::response('', 204)]);

        Prijzen::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'prijzen/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Prijzen::get('non-existent-uuid');
    }
}
