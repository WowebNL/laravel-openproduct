<?php

namespace Woweb\Openproduct\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Woweb\Openproduct\OpenProductServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            OpenProductServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('openproduct.url', env('OPENPRODUCT_URL', 'https://open-product.test/'));
        $app['config']->set('openproduct.auth_token', env('OPENPRODUCT_AUTH_TOKEN', 'test-token'));
        $app['config']->set('openproduct.language', env('OPENPRODUCT_LANGUAGE', 'nl'));
    }
}
