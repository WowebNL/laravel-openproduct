<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Organisaties
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'organisaties')
        )->json();
    }

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'organisaties/' . $uuid)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'naam'          => 'required|string|max:255',
            'code'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:254',
            'telefoonnummer' => 'nullable|string|max:15',
            'straat'        => 'nullable|string|max:255',
            'huisnummer'    => 'nullable|string|max:10',
            'postcode'      => ['nullable', 'string', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'stad'          => 'nullable|string|max:255',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'organisaties', $data)
        )->json();
    }

    public static function update(string $uuid, array $data): array
    {
        self::validateData($data, [
            'naam'          => 'required|string|max:255',
            'code'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:254',
            'telefoonnummer' => 'nullable|string|max:15',
            'straat'        => 'nullable|string|max:255',
            'huisnummer'    => 'nullable|string|max:10',
            'postcode'      => ['nullable', 'string', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'stad'          => 'nullable|string|max:255',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'organisaties/' . $uuid, $data)
        )->json();
    }

    public static function patch(string $uuid, array $data): array
    {
        self::validateData($data, [
            'naam'          => 'nullable|string|max:255',
            'code'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:254',
            'telefoonnummer' => 'nullable|string|max:15',
            'straat'        => 'nullable|string|max:255',
            'huisnummer'    => 'nullable|string|max:10',
            'postcode'      => ['nullable', 'string', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'stad'          => 'nullable|string|max:255',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'organisaties/' . $uuid, $data)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'organisaties/' . $uuid)
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
