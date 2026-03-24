<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Organisaties;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class OrganisatiesTest extends TestCase
{
    private function validOrganisatieData(): array
    {
        return [
            'naam' => 'Gemeente Nijmegen',
            'code' => 'GEM-NIJMEGEN',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Gemeente A']], 200)]);

        $result = Organisaties::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/organisaties'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Organisaties::list(['naam' => 'Gemeente']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'naam=Gemeente'));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440080';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Gemeente Nijmegen'], 200)]);

        $result = Organisaties::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'organisaties/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Gemeente Nijmegen'], 201)]);

        $result = Organisaties::create($this->validOrganisatieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/organisaties'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validOrganisatieData();
        unset($data['naam']);

        Organisaties::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_code(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validOrganisatieData();
        unset($data['code']);

        Organisaties::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440080';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Organisaties::update($uuid, $this->validOrganisatieData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'organisaties/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440080';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Patched'], 200)]);

        $result = Organisaties::patch($uuid, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'organisaties/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440080';
        Http::fake(['*' => Http::response('', 204)]);

        Organisaties::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'organisaties/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Organisaties::get('non-existent-uuid');
    }
}
