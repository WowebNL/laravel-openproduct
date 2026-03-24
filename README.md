# laravel-openproduct

[![Tests](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml/badge.svg)](https://github.com/WowebNL/laravel-openproduct/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)
[![PHP Version](https://img.shields.io/packagist/php-v/woweb/laravel-openproduct.svg)](https://packagist.org/packages/woweb/laravel-openproduct)

Laravel wrapper for the Product API and Producttypes API based on [Open Product](https://github.com/maykinmedia/open-product).

## Supported API versions

| API | Version |
|-----|---------|
| Producten API | v1.4.0 |
| Producttypen API | v1.4.0 |

## Requirements

- PHP 8.2 or higher
- Laravel 10, 11, or 12

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
OPENPRODUCT_LANGUAGE=nl
```

`OPENPRODUCT_LANGUAGE` sets the `Accept-Language` header on every API request. Defaults to `nl`.

## Upgrading from v1.x

Method names were changed in v2 to be consistent across all classes. Update your call sites:

| Old (v1) | New (v2) |
|---|---|
| `Producten::getAllProducten()` | `Producten::list()` |
| `Producten::getSingleProduct($uuid)` | `Producten::get($uuid)` |
| `Producten::createProduct($data)` | `Producten::create($data)` |
| `Producten::updateProduct($uuid, $data)` | `Producten::patch($uuid, $data)` |
| `ProductTypen::getAllProducttypes()` | `ProductTypen::list()` |
| `ProductTypen::getSingleProducttype($uuid)` | `ProductTypen::get($uuid)` |
| `ProductTypen::updateProducttype($uuid, $data)` | `ProductTypen::patch($uuid, $data)` |

## Usage

All classes throw `OpenProductValidationException` for invalid input before the API call is made, and `OpenProductException` for HTTP errors returned by the API.

### Error handling

```php
use Woweb\Openproduct\Exceptions\OpenProductException;
use Woweb\Openproduct\Exceptions\OpenProductValidationException;

try {
    $product = Producten::create($data);
} catch (OpenProductValidationException $e) {
    // Validation error in the provided data (before the API call)
    logger()->error($e->getMessage());
} catch (OpenProductException $e) {
    // HTTP error from the API (e.g. 404, 500)
    logger()->error('API error ' . $e->getCode() . ': ' . $e->getMessage());
}
```

### Producten

Endpoint: `producten/api/v1/producten`

```php
use Woweb\Openproduct\Api\Producten;

// List products (optional filters)
$producten = Producten::list(['status' => 'actief', 'page' => 1]);

// Get a single product
$product = Producten::get('550e8400-e29b-41d4-a716-446655440000');

// Create a product
$product = Producten::create([
    'producttype_uuid' => '550e8400-e29b-41d4-a716-446655440001',
    'eigenaren'        => [['bsn' => '123456789']],
    'naam'             => 'Parking permit',
    'start_datum'      => '2026-01-01',
    'status'           => 'actief',
    'frequentie'       => 'eenmalig',
]);

// Full update (PUT)
$product = Producten::update('550e8400-...', [
    'producttype_uuid' => '550e8400-...',
    'eigenaren'        => [['bsn' => '123456789']],
]);

// Partial update (PATCH)
$product = Producten::patch('550e8400-...', ['status' => 'ingetrokken']);

// Delete
Producten::delete('550e8400-...');
```

Valid values for `status`: `initieel`, `in_aanvraag`, `gereed`, `actief`, `ingetrokken`, `geweigerd`, `verlopen`.
Valid values for `frequentie`: `eenmalig`, `maandelijks`, `jaarlijks`.

### ProductTypen

Endpoint: `producttypen/api/v1/producttypen`

```php
use Woweb\Openproduct\Api\ProductTypen;

// List product types (optional filters)
$typen = ProductTypen::list(['doelgroep' => 'burgers']);

// Get a single product type
$type = ProductTypen::get('550e8400-...');

// Create
$type = ProductTypen::create([
    'doelgroep'    => 'burgers',
    'thema_uuids'  => ['497f6eca-...'],
    'naam'         => 'Parkeervergunning',
    'samenvatting' => 'Vergunning voor parkeren in de stad.',
    'code'         => 'PT-PARKEER',
]);

// Full update (PUT)
$type = ProductTypen::update('550e8400-...', [...]);

// Partial update (PATCH)
$type = ProductTypen::patch('550e8400-...', ['naam' => 'Updated name']);

// Delete
ProductTypen::delete('550e8400-...');

// Get the current/active price for a product type
$prijs = ProductTypen::getActuelePrijs('550e8400-...');

// Get current prices for all product types
$prijzen = ProductTypen::getAllActuelePrijzen();

// Get content blocks linked to a product type
$content = ProductTypen::getContent('550e8400-...', ['taal' => 'nl']);

// Create or replace a translation (PUT)
$vertaling = ProductTypen::updateVertaling('550e8400-...', 'en', [
    'naam'         => 'Parking permit',
    'samenvatting' => 'Permit for parking in the city.',
]);

// Partial translation update (PATCH)
$vertaling = ProductTypen::patchVertaling('550e8400-...', 'en', ['naam' => 'Parking permit']);

// Delete a translation
ProductTypen::deleteVertaling('550e8400-...', 'en');
```

Valid values for `doelgroep`: `burgers`, `bedrijven`, `burgers_en_bedrijven`.
`code` must match the pattern `^[A-Z0-9-]+$`.

### Themas

Endpoint: `producttypen/api/v1/themas`

```php
use Woweb\Openproduct\Api\Themas;

$themas = Themas::list();
$thema  = Themas::get('550e8400-...');
$thema  = Themas::create(['naam' => 'Wonen & Leven', 'producttype_uuids' => []]);
$thema  = Themas::update('550e8400-...', ['naam' => 'Updated', 'producttype_uuids' => []]);
$thema  = Themas::patch('550e8400-...', ['naam' => 'Patched']);
Themas::delete('550e8400-...');
```

### Content

Endpoint: `producttypen/api/v1/content`

```php
use Woweb\Openproduct\Api\Content;

$content = Content::get('550e8400-...');
$content = Content::create(['content' => '<p>Beschrijving</p>', 'producttype_uuid' => '550e8400-...']);
$content = Content::update('550e8400-...', ['content' => '<p>Updated</p>', 'producttype_uuid' => '550e8400-...']);
$content = Content::patch('550e8400-...', ['content' => '<p>Patched</p>']);
Content::delete('550e8400-...');

// Translations
$vertaling = Content::updateVertaling('550e8400-...', 'en', ['content' => '<p>Description</p>']);
Content::deleteVertaling('550e8400-...', 'en');
```

### ContentLabels

Endpoint: `producttypen/api/v1/contentlabels` (read-only)

```php
use Woweb\Openproduct\Api\ContentLabels;

$labels = ContentLabels::list(['page' => 1]);
```

### Prijzen

Endpoint: `producttypen/api/v1/prijzen`

```php
use Woweb\Openproduct\Api\Prijzen;

$prijzen = Prijzen::list(['producttype_uuid' => '550e8400-...']);
$prijs   = Prijzen::get('550e8400-...');
$prijs   = Prijzen::create(['producttype_uuid' => '550e8400-...', 'actief_vanaf' => '2026-01-01']);
$prijs   = Prijzen::update('550e8400-...', ['producttype_uuid' => '550e8400-...', 'actief_vanaf' => '2026-01-01']);
$prijs   = Prijzen::patch('550e8400-...', ['actief_vanaf' => '2026-06-01']);
Prijzen::delete('550e8400-...');
```

### Schemas

Endpoint: `producttypen/api/v1/schemas`

Note: schemas use an integer `$id`, not a UUID.

```php
use Woweb\Openproduct\Api\Schemas;

$schemas = Schemas::list();
$schema  = Schemas::get(42);
$schema  = Schemas::create(['naam' => 'Aanvraagschema', 'schema' => ['type' => 'object']]);
$schema  = Schemas::update(42, ['naam' => 'Updated', 'schema' => ['type' => 'object']]);
$schema  = Schemas::patch(42, ['naam' => 'Patched']);
Schemas::delete(42);
```

### Links

Endpoint: `producttypen/api/v1/links`

```php
use Woweb\Openproduct\Api\Links;

$links = Links::list(['producttype_uuid' => '550e8400-...']);
$link  = Links::get('550e8400-...');
$link  = Links::create([
    'naam'             => 'Meer informatie',
    'url'              => 'https://example.com/info',
    'producttype_uuid' => '550e8400-...',
]);
$link  = Links::update('550e8400-...', ['naam' => 'Updated', 'url' => 'https://example.com', 'producttype_uuid' => '550e8400-...']);
$link  = Links::patch('550e8400-...', ['naam' => 'Patched']);
Links::delete('550e8400-...');
```

### Bestanden

Endpoint: `producttypen/api/v1/bestanden`

File uploads use multipart form data automatically.

```php
use Woweb\Openproduct\Api\Bestanden;

$bestanden = Bestanden::list(['producttype_uuid' => '550e8400-...']);
$bestand   = Bestanden::get('550e8400-...');

// Upload a file
$bestand = Bestanden::create('/path/to/file.pdf', '550e8400-...');

// Replace a file (PUT)
$bestand = Bestanden::update('550e8400-...', '/path/to/new-file.pdf', '550e8400-...');

// Partially update (PATCH, file and/or producttype_uuid optional)
$bestand = Bestanden::patch('550e8400-...', '/path/to/file.pdf');

Bestanden::delete('550e8400-...');
```

### Acties

Endpoint: `producttypen/api/v1/acties`

```php
use Woweb\Openproduct\Api\Acties;

$acties = Acties::list(['producttype_uuid' => '550e8400-...']);
$actie  = Acties::get('550e8400-...');
$actie  = Acties::create([
    'naam'             => 'Indienen aanvraag',
    'tabel_endpoint'   => 'https://beslistabellen.example.com/pt-001',
    'dmn_tabel_id'     => 'pt-001-aanvraag',
    'producttype_uuid' => '550e8400-...',
]);
$actie  = Acties::update('550e8400-...', [...]);
$actie  = Acties::patch('550e8400-...', ['naam' => 'Patched']);
Acties::delete('550e8400-...');
```

### Locaties

Endpoint: `producttypen/api/v1/locaties`

```php
use Woweb\Openproduct\Api\Locaties;

$locaties = Locaties::list(['stad' => 'Nijmegen']);
$locatie  = Locaties::get('550e8400-...');
$locatie  = Locaties::create([
    'naam'       => 'Stadskantoor Nijmegen',
    'straat'     => 'Mariënburg',
    'huisnummer' => '75',
    'postcode'   => '6511 PS',
    'stad'       => 'Nijmegen',
]);
$locatie  = Locaties::update('550e8400-...', [...]);
$locatie  = Locaties::patch('550e8400-...', ['stad' => 'Arnhem']);
Locaties::delete('550e8400-...');
```

`postcode` must match the Dutch format `^[1-9][0-9]{3}\s?[A-Za-z]{2}$` (e.g. `6511 PS` or `6511PS`).

### Organisaties

Endpoint: `producttypen/api/v1/organisaties`

```php
use Woweb\Openproduct\Api\Organisaties;

$organisaties = Organisaties::list(['naam' => 'Gemeente']);
$organisatie  = Organisaties::get('550e8400-...');
$organisatie  = Organisaties::create(['naam' => 'Gemeente Nijmegen', 'code' => 'GEM-NIJMEGEN']);
$organisatie  = Organisaties::update('550e8400-...', ['naam' => 'Updated', 'code' => 'GEM-NMG']);
$organisatie  = Organisaties::patch('550e8400-...', ['naam' => 'Patched']);
Organisaties::delete('550e8400-...');
```

### Contacten

Endpoint: `producttypen/api/v1/contacten`

```php
use Woweb\Openproduct\Api\Contacten;

$contacten = Contacten::list(['naam' => 'Jan']);
$contact   = Contacten::get('550e8400-...');
$contact   = Contacten::create([
    'naam'             => 'Jan de Vries',
    'email'            => 'jan@example.com',       // optional
    'telefoonnummer'   => '0612345678',             // optional
    'rol'              => 'Contactpersoon',          // optional
    'organisatie_uuid' => '550e8400-...',           // optional
]);
$contact = Contacten::update('550e8400-...', ['naam' => 'Updated']);
$contact = Contacten::patch('550e8400-...', ['email' => 'nieuw@example.com']);
Contacten::delete('550e8400-...');
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

EUPL-1.2. See [LICENSE](LICENSE.md) for details.
