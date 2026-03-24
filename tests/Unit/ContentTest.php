<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\Content;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;
use Woweb\Openproduct\Tests\TestCase;

class ContentTest extends TestCase
{
    private function validContentData(): array
    {
        return [
            'content'          => '<p>Beschrijving</p>',
            'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440020',
        ];
    }

    public function test_get_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'content' => '<p>Test</p>'], 200)]);

        $result = Content::get($uuid);

        $this->assertIsArray($result);
        $this->assertSame($uuid, $result['uuid']);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/content/' . $uuid));
    }

    public function test_create_sends_post_and_returns_array(): void
    {
        Http::fake(['*' => Http::response(['uuid' => 'new-uuid', 'content' => '<p>Beschrijving</p>'], 201)]);

        $result = Content::create($this->validContentData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'POST' && str_contains($req->url(), 'producttypen/api/v1/content'));
    }

    public function test_create_throws_validation_exception_for_missing_content(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validContentData();
        unset($data['content']);

        Content::create($data);
    }

    public function test_create_throws_validation_exception_for_missing_producttype_uuid(): void
    {
        $this->expectException(OpenProductValidationException::class);

        $data = $this->validContentData();
        unset($data['producttype_uuid']);

        Content::create($data);
    }

    public function test_update_sends_put_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'content' => '<p>Updated</p>'], 200)]);

        $result = Content::update($uuid, $this->validContentData());

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'content/' . $uuid));
    }

    public function test_patch_sends_patch_and_returns_array(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'content' => '<p>Patched</p>'], 200)]);

        $result = Content::patch($uuid, ['content' => '<p>Patched</p>']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PATCH' && str_contains($req->url(), 'content/' . $uuid));
    }

    public function test_delete_sends_delete_request(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response('', 204)]);

        Content::delete($uuid);

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'content/' . $uuid));
    }

    public function test_update_vertaling_sends_put(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response(['uuid' => $uuid, 'content' => '<p>Description</p>'], 200)]);

        $result = Content::updateVertaling($uuid, 'en', ['content' => '<p>Description</p>']);

        $this->assertIsArray($result);
        Http::assertSent(fn($req) => $req->method() === 'PUT' && str_contains($req->url(), 'content/' . $uuid . '/vertaling/en'));
    }

    public function test_update_vertaling_throws_validation_exception_for_missing_content(): void
    {
        $this->expectException(OpenProductValidationException::class);

        Content::updateVertaling('some-uuid', 'en', []);
    }

    public function test_delete_vertaling_sends_delete(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440020';
        Http::fake(['*' => Http::response('', 204)]);

        Content::deleteVertaling($uuid, 'en');

        Http::assertSent(fn($req) => $req->method() === 'DELETE' && str_contains($req->url(), 'content/' . $uuid . '/vertaling/en'));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Not found'], 404)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(404);

        Content::get('non-existent-uuid');
    }
}
