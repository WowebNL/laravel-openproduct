<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class ProductTypen
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function getAllProducttypes(): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producttypen')
        )->json();
    }

    public static function getSingleProducttype(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producttypen/' . $uuid)
        )->json();
    }

    public static function updateProducttype(string $uuid, array $data): array
    {
        $data = self::validateUpdateProducttypeData($data);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'producttypen/' . $uuid, $data)
        )->json();
    }

    private static function validateResponse(Response $response): Response
    {
        if ($response->failed()) {
            throw OpenProductException::requestFailed($response->status(), $response->json());
        }

        return $response;
    }

    private static function validateUpdateProducttypeData(array $data): array
    {
        return $data;
    }
}
