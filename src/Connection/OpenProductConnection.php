<?php

namespace Woweb\Openproduct\Connection;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenProductConnection
{
    public static function getConnection(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization'   => 'Token ' . self::getAuthToken(),
            'Content-Type'    => 'application/json',
            'Accept-Language' => self::getLanguage(),
        ])->baseUrl(self::getBaseUrl());
    }

    public static function getMultipartConnection(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization'   => 'Token ' . self::getAuthToken(),
            'Accept-Language' => self::getLanguage(),
        ])->baseUrl(self::getBaseUrl());
    }

    private static function getBaseUrl(): string
    {
        return config('openproduct.url');
    }

    private static function getAuthToken(): ?string
    {
        return config('openproduct.auth_token');
    }

    private static function getLanguage(): string
    {
        return config('openproduct.language', 'nl');
    }
}
