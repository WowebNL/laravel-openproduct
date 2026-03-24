<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Locaties;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class LocatiesTest extends TestCase
{
    private function validLocatieData(): array
    {
        return [
            'naam'    => 'Stadskantoor Nijmegen',
            'straat'  => 'Mariënburg',
            'huisnummer' => '75',
            'postcode' => '6511 PS',
            'stad'    => 'Nijmegen',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Locatie A']], 200)]);

        $result = Locaties::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/locaties'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Locaties::list(['stad' => 'Nijmegen']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'stad=Nijmegen'));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440070';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Locatie A'], 200)]);

        $result = Locaties::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'locaties/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Stadskantoor Nijmegen'], 201)]);

        $result = Locaties::create($this->validLocatieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/locaties'));
    }

    public function test_create_accepts_postcode_without_space(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid'], 201)]);

        $data = $this->validLocatieData();
        $data['postcode'] = '6511PS';

        $result = Locaties::create($data);

        $this->assertIsArray($result);
    }

    public function test_create_throws_validation_exception_for_invalid_postcode(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validLocatieData();
        $data['postcode'] = '0511PS';  // invalid: starts with 0

        Locaties::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440070';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Locaties::update($uuid, $this->validLocatieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'locaties/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440070';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'stad' => 'Arnhem'], 200)]);

        $result = Locaties::patch($uuid, ['stad' => 'Arnhem']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'locaties/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440070';
        Http::fake(['*' => Http::response('', 204)]);

        Locaties::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'locaties/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Locaties::get('non-existent-uuid');
    }
}
