<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Content
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'content/' . $uuid)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'content'              => 'required|string|min:1',
            'producttype_uuid'     => 'required|uuid',
            'aanvullende_informatie' => 'nullable|string|min:1',
            'labels'               => 'nullable|array',
            'labels.*'             => 'string|min:1',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'content', $data)
        )->json();
    }

    public static function update(string $uuid, array $data): array
    {
        self::validateData($data, [
            'content'              => 'required|string|min:1',
            'producttype_uuid'     => 'required|uuid',
            'aanvullende_informatie' => 'nullable|string|min:1',
            'labels'               => 'nullable|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'content/' . $uuid, $data)
        )->json();
    }

    public static function patch(string $uuid, array $data): array
    {
        self::validateData($data, [
            'content'              => 'nullable|string|min:1',
            'producttype_uuid'     => 'nullable|uuid',
            'aanvullende_informatie' => 'nullable|string|min:1',
            'labels'               => 'nullable|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'content/' . $uuid, $data)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'content/' . $uuid)
        );
    }

    public static function updateVertaling(string $uuid, string $taal, array $data): array
    {
        self::validateData($data, [
            'content'               => 'required|string|min:1',
            'aanvullende_informatie' => 'nullable|string|min:1',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'content/' . $uuid . '/vertaling/' . $taal, $data)
        )->json();
    }

    public static function deleteVertaling(string $uuid, string $taal): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'content/' . $uuid . '/vertaling/' . $taal)
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
