<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;

class ContentLabels
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'contentlabels')
        )->json();
    }

    private static function validateResponse(Response $response): Response
    {
        if ($response->failed()) {
            throw OpenProductException::requestFailed($response->status(), $response->json());
        }

        return $response;
    }
}
