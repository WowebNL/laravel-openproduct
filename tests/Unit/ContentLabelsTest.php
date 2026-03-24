<?php

namespace Woweb\Openproduct\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Woweb\Openproduct\Api\ContentLabels;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Tests\TestCase;

class ContentLabelsTest extends TestCase
{
    public function test_list_returns_array(): void
    {
        Http::fake(['*' => Http::response([['id' => 1, 'label' => 'Beschrijving']], 200)]);

        $result = ContentLabels::list();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        Http::assertSent(fn($req) => str_contains($req->url(), 'producttypen/api/v1/contentlabels'));
    }

    public function test_list_with_filters_passes_query_params(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        ContentLabels::list(['page' => 2]);

        Http::assertSent(fn($req) => str_contains($req->url(), 'page=2'));
    }

    public function test_api_failure_throws_openproduct_exception(): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Server error'], 500)]);

        $this->expectException(OpenProductException::class);
        $this->expectExceptionCode(500);

        ContentLabels::list();
    }
}
