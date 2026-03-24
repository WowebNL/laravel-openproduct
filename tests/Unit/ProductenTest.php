<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Producten;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class ProductenTest extends TestCase
{
    private function validProductData(): array
    {
        return [
            'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'eigenaren'        => [['bsn' => '123456789']],
            'naam'             => 'Test Product',
            'start_datum'      => '2026-01-01',
            'eind_datum'       => '2026-12-31',
            'status'           => 'actief',
            'frequentie'       => 'eenmalig',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => '123', 'naam' => 'Test']], 200)]);

        $result = Producten::list();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producten/api/v1/producten'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Producten::list(['status' => 'actief', 'page' => 1]);

        Http::assertSent(fn($req) => str_contains($req->url(), 'status=actief'));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Test'], 200)]);

        $result = Producten::get($uuid);

        $this->assertIsArray($result);
        $this->assertSame($uuid, $result['uuid']);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producten/api/v1/producten/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Test Product'], 201)]);

        $result = Producten::create($this->validProductData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producten/api/v1/producten'));
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        unset($data['producttype_uuid']);

        Producten::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_eigenaren(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        unset($data['eigenaren']);

        Producten::create($data);
    }

    public function test_create_throws_validation_exception_for_invalid_status(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        $data['status'] = 'invalid-status';

        Producten::create($data);
    }

    public function test_create_throws_validation_exception_for_invalid_frequentie(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        $data['frequentie'] = 'dagelijks';

        Producten::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Producten::update($uuid, $this->validProductData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'producten/' . $uuid));
    }

    public function test_update_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        unset($data['producttype_uuid']);

        Producten::update('some-uuid', $data);
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'status' => 'ingetrokken'], 200)]);

        $result = Producten::patch($uuid, ['status' => 'ingetrokken']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'producten/' . $uuid));
    }

    public function test_patch_throws_validation_exception_for_invalid_status(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Producten::patch('some-uuid', ['status' => 'ongeldig']);
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response('', 204)]);

        Producten::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'producten/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Producten::get('non-existent-uuid');
    }
}
