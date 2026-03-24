<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Producten
{
    const API_ENDPOINT = 'producten/api/v1/';

    public static function getAllProducten(): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producten')
        )->json();
    }

    public static function getSingleProduct(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producten/' . $uuid)
        )->json();
    }

    public static function createProduct(array $data): array
    {
        $data = self::validateCreateProductData($data);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'producten', $data)
        )->json();
    }

    public static function updateProduct(string $uuid, array $data): array
    {
        $data = self::validateUpdateProductData($data);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'producten/' . $uuid, $data)
        )->json();
    }

    private static function validateResponse(Response $response): Response
    {
        if ($response->failed()) {
            throw OpenProductException::requestFailed($response->status(), $response->json());
        }

        return $response;
    }

    private static function validateCreateProductData(array $data): array
    {
        $validator = validator($data, [
            'naam'                => 'required|string|max:255',
            'start_datum'         => 'required|date',
            'eind_datum'          => 'nullable|date',
            'producttype_uuid'    => 'required|uuid',
            'eigenaren.0.bsn'     => 'required|string|size:9',
            'aanvraag_zaak_url'   => 'required|url',
            'status'              => 'required|string|in:actief,verlopen',
            'frequentie'          => 'required|string|in:eenmalig,maandelijks',
            'dataobject.location' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw OpenProductValidationException::fromErrors($validator->errors()->all());
        }

        return $validator->validated();
    }

    private static function validateUpdateProductData(array $data): array
    {
        $validator = validator($data, [
            'status' => 'required|string|in:actief,ingetrokken,geweigerd',
        ]);

        if ($validator->fails()) {
            throw OpenProductValidationException::fromErrors($validator->errors()->all());
        }

        return $validator->validated();
    }
}
