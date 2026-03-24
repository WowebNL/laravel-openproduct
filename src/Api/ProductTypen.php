<?php

namespace Woweb\Openproduct\Api;

use Illuminate\Http\Client\Response;
use Woweb\Openproduct\Connection\OpenProductConnection;
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

class ProductTypen
{
    const API_ENDPOINT = 'producttypen/api/v1/';

    public static function list(array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'producttypen')
        )->json();
    }

    public static function get(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producttypen/' . $uuid)
        )->json();
    }

    public static function create(array $data): array
    {
        self::validateData($data, [
            'doelgroep'                  => 'required|string|in:burgers,interne_organisatie,samenwerkingspartners,bedrijven_en_instellingen',
            'thema_uuids'                => 'required|array|min:1',
            'thema_uuids.*'              => 'uuid',
            'naam'                       => 'required|string|max:255',
            'samenvatting'               => 'required|string|min:1',
            'code'                       => ['required', 'string', 'max:255', 'regex:/^[A-Z0-9-]+$/'],
            'uniforme_product_naam'      => 'nullable|string|min:1',
            'locatie_uuids'              => 'nullable|array',
            'locatie_uuids.*'            => 'uuid',
            'organisatie_uuids'          => 'nullable|array',
            'organisatie_uuids.*'        => 'uuid',
            'contact_uuids'              => 'nullable|array',
            'contact_uuids.*'            => 'uuid',
            'externe_codes'              => 'nullable|array',
            'parameters'                 => 'nullable|array',
            'zaaktypen'                  => 'nullable|array',
            'verzoektypen'               => 'nullable|array',
            'processen'                  => 'nullable|array',
            'verbruiksobject_schema_naam' => 'nullable|string|min:1',
            'dataobject_schema_naam'     => 'nullable|string|min:1',
            'publicatie_start_datum'     => 'nullable|date',
            'publicatie_eind_datum'      => 'nullable|date',
            'toegestane_statussen'       => 'nullable|array',
            'toegestane_statussen.*'     => 'string|in:in_aanvraag,gereed,actief,ingetrokken,geweigerd,verlopen',
            'keywords'                   => 'nullable|array',
            'keywords.*'                 => 'string|max:100',
            'interne_opmerkingen'        => 'nullable|string',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->post(self::API_ENDPOINT . 'producttypen', $data)
        )->json();
    }

    public static function update(string $uuid, array $data): array
    {
        self::validateData($data, [
            'doelgroep'                  => 'required|string|in:burgers,interne_organisatie,samenwerkingspartners,bedrijven_en_instellingen',
            'thema_uuids'                => 'required|array|min:1',
            'thema_uuids.*'              => 'uuid',
            'naam'                       => 'required|string|max:255',
            'samenvatting'               => 'required|string|min:1',
            'code'                       => ['required', 'string', 'max:255', 'regex:/^[A-Z0-9-]+$/'],
            'uniforme_product_naam'      => 'nullable|string|min:1',
            'locatie_uuids'              => 'nullable|array',
            'organisatie_uuids'          => 'nullable|array',
            'contact_uuids'              => 'nullable|array',
            'externe_codes'              => 'nullable|array',
            'parameters'                 => 'nullable|array',
            'zaaktypen'                  => 'nullable|array',
            'verzoektypen'               => 'nullable|array',
            'processen'                  => 'nullable|array',
            'verbruiksobject_schema_naam' => 'nullable|string|min:1',
            'dataobject_schema_naam'     => 'nullable|string|min:1',
            'publicatie_start_datum'     => 'nullable|date',
            'publicatie_eind_datum'      => 'nullable|date',
            'toegestane_statussen'       => 'nullable|array',
            'keywords'                   => 'nullable|array',
            'interne_opmerkingen'        => 'nullable|string',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'producttypen/' . $uuid, $data)
        )->json();
    }

    public static function patch(string $uuid, array $data): array
    {
        self::validateData($data, [
            'doelgroep'                  => 'nullable|string|in:burgers,interne_organisatie,samenwerkingspartners,bedrijven_en_instellingen',
            'thema_uuids'                => 'nullable|array',
            'naam'                       => 'nullable|string|max:255',
            'samenvatting'               => 'nullable|string',
            'code'                       => ['nullable', 'string', 'max:255', 'regex:/^[A-Z0-9-]+$/'],
            'uniforme_product_naam'      => 'nullable|string',
            'locatie_uuids'              => 'nullable|array',
            'organisatie_uuids'          => 'nullable|array',
            'contact_uuids'              => 'nullable|array',
            'externe_codes'              => 'nullable|array',
            'parameters'                 => 'nullable|array',
            'zaaktypen'                  => 'nullable|array',
            'verzoektypen'               => 'nullable|array',
            'processen'                  => 'nullable|array',
            'publicatie_start_datum'     => 'nullable|date',
            'publicatie_eind_datum'      => 'nullable|date',
            'toegestane_statussen'       => 'nullable|array',
            'keywords'                   => 'nullable|array',
            'interne_opmerkingen'        => 'nullable|string',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'producttypen/' . $uuid, $data)
        )->json();
    }

    public static function delete(string $uuid): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'producttypen/' . $uuid)
        );
    }

    public static function getActuelePrijs(string $uuid): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producttypen/' . $uuid . '/actuele-prijs')
        )->json();
    }

    public static function getAllActuelePrijzen(): array
    {
        return self::validateResponse(
            OpenProductConnection::getConnection()->get(self::API_ENDPOINT . 'producttypen/actuele-prijzen')
        )->json();
    }

    public static function getContent(string $uuid, array $filters = []): array
    {
        $request = OpenProductConnection::getConnection();

        if (! empty($filters)) {
            $request = $request->withQueryParameters($filters);
        }

        return self::validateResponse(
            $request->get(self::API_ENDPOINT . 'producttypen/' . $uuid . '/content')
        )->json();
    }

    public static function updateVertaling(string $uuid, string $taal, array $data): array
    {
        self::validateData($data, [
            'naam'        => 'required|string|max:255',
            'samenvatting' => 'required|string|min:1',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->put(self::API_ENDPOINT . 'producttypen/' . $uuid . '/vertaling/' . $taal, $data)
        )->json();
    }

    public static function patchVertaling(string $uuid, string $taal, array $data): array
    {
        self::validateData($data, [
            'naam'        => 'nullable|string|max:255',
            'samenvatting' => 'nullable|string',
        ]);

        return self::validateResponse(
            OpenProductConnection::getConnection()->patch(self::API_ENDPOINT . 'producttypen/' . $uuid . '/vertaling/' . $taal, $data)
        )->json();
    }

    public static function deleteVertaling(string $uuid, string $taal): void
    {
        self::validateResponse(
            OpenProductConnection::getConnection()->delete(self::API_ENDPOINT . 'producttypen/' . $uuid . '/vertaling/' . $taal)
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
