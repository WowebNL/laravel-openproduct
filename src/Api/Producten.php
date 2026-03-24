<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class Producten
{
    const API_ENDPOINT = 'producten/api/v1/';

    public static function list(array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'producten')
        )->json();
    }

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producten/' . $uuid)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'producttype_uuid'  => 'required|uuid',
            'eigenaren'         => 'required|array|min:1',
            'naam'              => 'nullable|string|max:255',
            'start_datum'       => 'nullable|date',
            'eind_datum'        => 'nullable|date',
            'gepubliceerd'      => 'nullable|boolean',
            'status'            => 'nullable|string|in:initieel,in_aanvraag,gereed,actief,ingetrokken,geweigerd,verlopen',
            'prijs'             => ['nullable', 'regex:/^-?\d{0,6}(?:\.\d{0,2})?$/'],
            'frequentie'        => 'nullable|string|in:eenmalig,maandelijks,jaarlijks',
            'aanvraag_zaak_url' => 'nullable|url|max:200',
            'aanvraag_zaak_urn' => 'nullable|string',
            'verbruiksobject'   => 'nullable|array',
            'dataobject'        => 'nullable|array',
            'documenten'        => 'nullable|array',
            'zaken'             => 'nullable|array',
            'taken'             => 'nullable|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'producten', $data)
        )->json();
    }

    public static function update(string $uuid, array $data): array
    {
        self::validateData($data, [
            'producttype_uuid'  => 'required|uuid',
            'eigenaren'         => 'required|array|min:1',
            'naam'              => 'nullable|string|max:255',
            'start_datum'       => 'nullable|date',
            'eind_datum'        => 'nullable|date',
            'gepubliceerd'      => 'nullable|boolean',
            'status'            => 'nullable|string|in:initieel,in_aanvraag,gereed,actief,ingetrokken,geweigerd,verlopen',
            'prijs'             => ['nullable', 'regex:/^-?\d{0,6}(?:\.\d{0,2})?$/'],
            'frequentie'        => 'nullable|string|in:eenmalig,maandelijks,jaarlijks',
            'aanvraag_zaak_url' => 'nullable|url|max:200',
            'aanvraag_zaak_urn' => 'nullable|string',
            'verbruiksobject'   => 'nullable|array',
            'dataobject'        => 'nullable|array',
            'documenten'        => 'nullable|array',
            'zaken'             => 'nullable|array',
            'taken'             => 'nullable|array',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'producten/' . $uuid, $data)
        )->json();
    }

    public static function patch(string $uuid, array $data): array
    {
        self::validateData($data, [
            'producttype_uuid'  => 'nullable|uuid',
            'eigenaren'         => 'nullable|array',
            'naam'              => 'nullable|string|max:255',
            'start_datum'       => 'nullable|date',
            'eind_datum'        => 'nullable|date',
            'gepubliceerd'      => 'nullable|boolean',
            'status'            => 'nullable|string|in:initieel,in_aanvraag,gereed,actief,ingetrokken,geweigerd,verlopen',
            'prijs'             => ['nullable', 'regex:/^-?\d{0,6}(?:\.\d{0,2})?$/'],
            'frequentie'        => 'nullable|string|in:eenmalig,maandelijks,jaarlijks',
            'aanvraag_zaak_url' => 'nullable|url|max:200',
            'aanvraag_zaak_urn' => 'nullable|string',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'producten/' . $uuid, $data)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'producten/' . $uuid)
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
