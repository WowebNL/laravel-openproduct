<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Links
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'links', $filters)
        )->json();
    }

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'links/' . $uuid)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'naam'             => 'required|string|max:255',
            'url'              => 'required|url|max:200',
            'producttype_uuid' => 'required|uuid',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'links', $data)
        )->json();
    }

    public static function update(string $uuid, array $data): array
    {
        self::validateData($data, [
            'naam'             => 'required|string|max:255',
            'url'              => 'required|url|max:200',
            'producttype_uuid' => 'required|uuid',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'links/' . $uuid, $data)
        )->json();
    }

    public static function patch(string $uuid, array $data): array
    {
        self::validateData($data, [
            'naam'             => 'nullable|string|max:255',
            'url'              => 'nullable|url|max:200',
            'producttype_uuid' => 'nullable|uuid',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'links/' . $uuid, $data)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'links/' . $uuid)
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
