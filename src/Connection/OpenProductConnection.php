<?php

namespace Woweb\Openproduct\Connection;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenProductConnection
{
    /**
     * Get base connection for Open Product API.
     */
    public static function getConnection(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Token ' . self::getAuthToken(),
            'Content-Type'  => 'application/json',
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
}
