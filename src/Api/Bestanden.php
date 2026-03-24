<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Bestanden
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'bestanden', $filters)
        )->json();
    }

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'bestanden/' . $uuid)
        )->json();
    }

    public static function create(string $filePath, string $producttypeUuid): array
    {
        self::validateData(
            ['producttype_uuid' => $producttypeUuid, 'bestand' => $filePath],
            [
                'producttype_uuid' => 'required|uuid',
                'bestand'          => 'required|string',
            ]
        );

        if (! file_exists($filePath)) {
            throw \Woweb\Openproduct\Exceptions\OpenProductValidationException::fromErrors(["The file '{$filePath}' does not exist."]);
        }

        return self::validateResponse(
            OpenProductConnection::getMultipartConnection()
                ->attach('bestand', file_get_contents($filePath), basename($filePath))
                ->post(self::API_ENDPOINT . 'bestanden', ['producttype_uuid' => $producttypeUuid])
        )->json();
    }

    public static function update(string $uuid, string $filePath, string $producttypeUuid): array
    {
        self::validateData(
            ['producttype_uuid' => $producttypeUuid],
            ['producttype_uuid' => 'required|uuid']
        );

        return self::validateResponse(
            OpenProductConnection::getMultipartConnection()
                ->attach('bestand', file_get_contents($filePath), basename($filePath))
                ->put(self::API_ENDPOINT . 'bestanden/' . $uuid, ['producttype_uuid' => $producttypeUuid])
        )->json();
    }

    public static function patch(string $uuid, ?string $filePath = null, ?string $producttypeUuid = null): array
    {
        self::validateData(
            ['producttype_uuid' => $producttypeUuid],
            ['producttype_uuid' => 'nullable|uuid']
        );

        $connection = OpenProductConnection::getMultipartConnection();
        $body       = [];

        if ($filePath !== null) {
            $connection = $connection->attach('bestand', file_get_contents($filePath), basename($filePath));
        }

        if ($producttypeUuid !== null) {
            $body['producttype_uuid'] = $producttypeUuid;
        }

        return self::validateResponse(
            $connection->patch(self::API_ENDPOINT . 'bestanden/' . $uuid, $body)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'bestanden/' . $uuid)
        );
    }

    private static function validateData(array $data, array $rules): void
    {
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw OpenProductValidationException::fromErrors($validator->errors()->all());
        }
    }

    private static function validateResponse(Response $response): Response
    {
        if ($response->failed()) {
            throw OpenProductException::requestFailed($response->status(), $response->json());
        }

        return $response;
    }
}
