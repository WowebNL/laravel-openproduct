<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Schemas
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'schemas')
        )->json();
    }

    public static function get(int $id): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'schemas/' . $id)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'naam'   => 'required|string|max:200',
            'schema' => 'required|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'schemas', $data)
        )->json();
    }

    public static function update(int $id, array $data): array
    {
        self::validateData($data, [
            'naam'   => 'required|string|max:200',
            'schema' => 'required|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'schemas/' . $id, $data)
        )->json();
    }

    public static function patch(int $id, array $data): array
    {
        self::validateData($data, [
            'naam'   => 'nullable|string|max:200',
            'schema' => 'nullable|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'schemas/' . $id, $data)
        )->json();
    }

    public static function delete(int $id): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'schemas/' . $id)
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
