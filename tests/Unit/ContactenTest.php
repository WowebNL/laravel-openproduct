<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Contacten;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class ContactenTest extends TestCase
{
    private function validContactData(): array
    {
        return [
            'naam' => 'Jan de Vries',
        ];
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'naam' => 'Jan de Vries']], 200)]);

        $result = Contacten::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/contacten'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Contacten::list(['naam' => 'Jan']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'naam=Jan'));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440090';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Jan de Vries'], 200)]);

        $result = Contacten::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'contacten/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Jan de Vries'], 201)]);

        $result = Contacten::create($this->validContactData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/contacten'));
    }

    public function test_create_throws_validation_exception_for_missing_naam(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Contacten::create([]);
    }

    public function test_create_with_optional_fields_succeeds(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'naam' => 'Jan de Vries'], 201)]);

        $data = $this->validContactData();
        $data['email'] = 'jan@example.com';
        $data['telefoonnummer'] = '0612345678';
        $data['rol'] = 'Contactpersoon';
        $data['organisatie_uuid'] = '550e8400-e29b-41d4-a716-446655440080';

        $result = Contacten::create($data);

        $this->assertIsArray($result);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440090';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Updated'], 200)]);

        $result = Contacten::update($uuid, $this->validContactData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'contacten/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440090';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'naam' => 'Patched'], 200)]);

        $result = Contacten::patch($uuid, ['naam' => 'Patched']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'contacten/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440090';
        Http::fake(['*' => Http::response('', 204)]);

        Contacten::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'contacten/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Server error'], 500)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(500);

        Contacten::get('non-existent-uuid');
    }
}
