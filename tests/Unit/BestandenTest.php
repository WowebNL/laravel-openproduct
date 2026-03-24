<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Bestanden;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class BestandenTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'openproduct_test_');
        file_put_contents($this->tempFile, 'dummy content');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['uuid' => 'abc', 'bestand' => 'file.pdf']], 200)]);

        $result = Bestanden::list();

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/bestanden'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        Bestanden::list(['producttype_uuid' => '550e8400-e29b-41d4-a716-446655440050']);

        Http::assertSent(fn($req) => str_contains($req->url(), 'producttype_uuid='));
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440050';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Bestanden::get($uuid);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'bestanden/' . $uuid));
    }

    public function test_create_sends_multipart_post(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid'], 201)]);

        $result = Bestanden::create($this->tempFile, '550e8400-e29b-41d4-a716-446655440050');

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/bestanden'));
    }

    public function test_create_throws_validation_exception_for_nonexistent_file(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Bestanden::create('/nonexistent/path/file.pdf', '550e8400-e29b-41d4-a716-446655440050');
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Bestanden::create($this->tempFile, '');
    }

    public function test_update_sends_multipart_put(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440050';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Bestanden::update($uuid, $this->tempFile, '550e8400-e29b-41d4-a716-446655440050');

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'bestanden/' . $uuid));
    }

    public function test_patch_sends_multipart_patch(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440050';
        Http::fake(['*' => Http::response(['uuid' => $uuid], 200)]);

        $result = Bestanden::patch($uuid, $this->tempFile);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'bestanden/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440050';
        Http::fake(['*' => Http::response('', 204)]);

        Bestanden::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'bestanden/' . $uuid));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Bestanden::get('non-existent-uuid');
    }
}
