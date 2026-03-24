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
            'naam'              => 'Test Product',
            'start_datum'       => '2026-01-01',
            'eind_datum'        => '2026-12-31',
            'producttype_uuid'  => '550e8400-e29b-41d4-a716-446655440000',
            'eigenaren'         => [['bsn' => '123456789']],
            'aanvraag_zaak_url' => 'https://zaak.example.com/api/zaken/1',
            'status'            => 'actief',
            'frequentie'        => 'eenmalig',
            'dataobject'        => ['location' => 'Nijmegen'],
        ];
    }

    public function test_get_all_producten_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => '123', 'naam' => 'Test']], 200)]);

        $result = Producten::getAllProducten();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producten/api/v1/producten'));
    }

    public function test_get_single_product_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Test'], 200)]);

        $result = Producten::getSingleProduct($uuid);

        $this->assertIsArray($result);
        $this->assertSame($uuid, $result['uuid']);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producten/api/v1/producten/' . $uuid));
    }

    public function test_create_product_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Test Product'], 201)]);

        $result = Producten::createProduct($this->validProductData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->isPost() && str_contains($req->url(), 'producten/api/v1/producten'));
    }

    public function test_create_product_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        unset($data['naam']);

        Producten::createProduct($data);
    }

    public function test_create_product_throws_validation_exception_for_invalid_status(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validProductData();
        $data['status'] = 'invalid-status';

        Producten::createProduct($data);
    }

    public function test_update_product_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'status' => 'ingetrokken'], 200)]);

        $result = Producten::updateProduct($uuid, ['status' => 'ingetrokken']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'producten/' . $uuid));
    }

    public function test_update_product_throws_validation_exception_for_invalid_status(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Producten::updateProduct('some-uuid', ['status' => 'ongeldig']);
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Producten::getSingleProduct('non-existent-uuid');
    }
}
