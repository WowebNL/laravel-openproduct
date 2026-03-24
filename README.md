# laravel-openproduct

[![Tests](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml/badge.svg)](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)
[![PHP Version](https://img.shields.io/packagist/php-v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)

Laravel wrapper voor de Open Product API (VNG/Common Ground).

## Vereisten

- PHP 8.2 of hoger
- Laravel 10 of hoger

## Installatie

```bash
composer require woweb/laravel-openproduct
```

De service provider wordt automatisch geregistreerd via Laravel's package auto-discovery.

Publiceer de configuratie:

```bash
php artisan vendor:publish --provider="Woweb\Openproduct\OpenProductServiceProvider"
```

## Configuratie

Voeg de volgende variabelen toe aan je `.env`:

```env
OPENPRODUCT_URL=https://jouw-openproduct-instantie.nl/open-product/
OPENPRODUCT_AUTH_TOKEN=jouw-api-token
```

Of pas `config/openproduct.php` direct aan.

## Gebruik

### Producten

```php
use Woweb\Openproduct\Api\Producten;

// Alle producten ophalen
$producten = Producten::getAllProducten();

// Enkel product ophalen
$product = Producten::getSingleProduct('550e8400-e29b-41d4-a716-446655440000');

// Product aanmaken
$product = Producten::createProduct([
    'naam'              => 'Parkeervergunning',
    'start_datum'       => '2026-01-01',
    'eind_datum'        => '2026-12-31',
    'producttype_uuid'  => '550e8400-e29b-41d4-a716-446655440000',
    'eigenaren'         => [['bsn' => '123456789']],
    'aanvraag_zaak_url' => 'https://zaken.example.com/api/zaken/1',
    'status'            => 'actief',
    'frequentie'        => 'eenmalig',
    'dataobject'        => ['location' => 'Nijmegen'],
]);

// Product bijwerken (status)
$product = Producten::updateProduct('550e8400-e29b-41d4-a716-446655440000', [
    'status' => 'ingetrokken',
]);
```

### Producttypen

```php
use Woweb\Openproduct\Api\ProductTypen;

// Alle producttypen ophalen
$typen = ProductTypen::getAllProducttypes();

// Enkel producttype ophalen
$type = ProductTypen::getSingleProducttype('550e8400-e29b-41d4-a716-446655440001');

// Producttype bijwerken
$type = ProductTypen::updateProducttype('550e8400-e29b-41d4-a716-446655440001', [
    'naam' => 'Gewijzigd type',
]);
```

### Foutafhandeling

Het package gooit eigen exceptions:

```php
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

try {
    $product = Producten::createProduct($data);
} catch (OpenProductValidationException $e) {
    // Validatiefout in de meegegeven data
    logger()->error($e->getMessage());
} catch (OpenProductException $e) {
    // HTTP-fout van de API (bijv. 404, 500)
    logger()->error('API fout ' . $e->getCode() . ': ' . $e->getMessage());
}
```

## Testen

```bash
composer install
vendor/bin/phpunit
```

## Licentie

EUPL-1.2. Zie [LICENSE](LICENSE) voor details.
