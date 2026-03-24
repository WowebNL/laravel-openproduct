# laravel-openproduct

[![Tests](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml/badge.svg)](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)
[![PHP Version](https://img.shields.io/packagist/php-v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)

Laravel wrapper for the Product API and Producttypes API based on [Open Product](https://github.com/maykinmedia/open-product).

## Requirements

- PHP 8.2 or higher
- Laravel 10 or higher

## Installation

```bash
composer require woweb/laravel-openproduct
```

The service provider is registered automatically via Laravel's package auto-discovery.

Publish the configuration:

```bash
php artisan vendor:publish --provider="Woweb\Openproduct\OpenProductServiceProvider"
```

## Configuration

Add the following variables to your `.env`:

```env
OPENPRODUCT_URL=https://your-openproduct-instance.nl/open-product/
OPENPRODUCT_AUTH_TOKEN=your-api-token
```

Or edit `config/openproduct.php` directly.

## Usage

### Products

```php
use Woweb\Openproduct\Api\Producten;

// Retrieve all products
$producten = Producten::getAllProducten();

// Retrieve a single product
$product = Producten::getSingleProduct('550e8400-e29b-41d4-a716-446655440000');

// Create a product
$product = Producten::createProduct([
    'naam'              => 'Parking permit',
    'start_datum'       => '2026-01-01',
    'eind_datum'        => '2026-12-31',
    'producttype_uuid'  => '550e8400-e29b-41d4-a716-446655440000',
    'eigenaren'         => [['bsn' => '123456789']],
    'aanvraag_zaak_url' => 'https://zaken.example.com/api/zaken/1',
    'status'            => 'actief',
    'frequentie'        => 'eenmalig',
    'dataobject'        => ['location' => 'Nijmegen'],
]);

// Update a product (status)
$product = Producten::updateProduct('550e8400-e29b-41d4-a716-446655440000', [
    'status' => 'ingetrokken',
]);
```

### Product types

```php
use Woweb\Openproduct\Api\ProductTypen;

// Retrieve all product types
$typen = ProductTypen::getAllProducttypes();

// Retrieve a single product type
$type = ProductTypen::getSingleProducttype('550e8400-e29b-41d4-a716-446655440001');

// Update a product type
$type = ProductTypen::updateProducttype('550e8400-e29b-41d4-a716-446655440001', [
    'naam' => 'Updated type',
]);
```

### Error handling

The package throws its own exceptions:

```php
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

try {
    $product = Producten::createProduct($data);
} catch (OpenProductValidationException $e) {
    // Validation error in the provided data
    logger()->error($e->getMessage());
} catch (OpenProductException $e) {
    // HTTP error from the API (e.g. 404, 500)
    logger()->error('API error ' . $e->getCode() . ': ' . $e->getMessage());
}
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

EUPL-1.2. See [LICENSE](LICENSE) for details.
