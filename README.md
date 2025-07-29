# PHP Exchange Rate SDK

A PHP SDK for the [Currency Exchange API](https://github.com/fawazahmed0/exchange-api). This package provides an easy-to-use interface to fetch current and historical exchange rates with built-in fallback support.

## Features

- 🚀 Easy to use static methods
- 💪 Built-in fallback support between JSDelivr and Cloudflare endpoints
- 📅 Support for historical exchange rates
- 🔄 Automatic retry with minified and regular JSON formats
- ⚡ Fast and reliable with multiple CDN options
- 🛠 Configurable endpoint preferences
- 🔍 Comprehensive error handling
- 💾 PSR-16 compatible caching support
- ⚙️ Customizable HTTP client configuration

## Installation

Install the package via Composer:

```bash
composer require php-core/exchange-sdk
```

## Quick Start

```php
use PHPCore\ExchangeSDK\Exchange;

// Get latest USD rates
$usdRates = Exchange::getLatestRates('usd');

// Get EUR/USD rate
$eurUsdRate = Exchange::getLatestRate('eur', 'usd');

// Get historical rates
$historicalRates = Exchange::getHistoricalRates('2024-03-06', 'usd');
```

## Usage Examples

### Getting Latest Exchange Rates

```php
use PHPCore\ExchangeSDK\Exchange;

// Get all rates for USD
$usdRates = Exchange::getLatestRates('usd');
// Returns: ['date' => '2024-03-06', 'usd' => ['eur' => 0.85, 'gbp' => 0.73, ...]]

// Get specific currency pair rate
$eurUsdRate = Exchange::getLatestRate('eur', 'usd');
// Returns: 1.18 (float)
```

### Getting Historical Rates

```php
// Get historical rates for a specific date
$historicalRates = Exchange::getHistoricalRates('2024-03-06', 'usd');
// Returns: ['date' => '2024-03-06', 'usd' => ['eur' => 0.84, ...]]

// Get historical rate for a specific currency pair
$historicalEurUsd = Exchange::getHistoricalRate('2024-03-06', 'eur', 'usd');
// Returns: 1.08854773 (float)
```

### Configuring Endpoints

```php
// Set Cloudflare as the preferred endpoint globally
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);

// Or use a specific endpoint for a single call
$rates = Exchange::getLatestRates('usd', Exchange::ENDPOINT_JSDELIVR);
```

### Caching Support

The SDK supports PSR-16 Simple Cache for storing exchange rates. By default, it uses Symfony's FilesystemAdapter.

#### Default Cache Configuration

```php
// Initialize with default filesystem cache (24-hour TTL)
Exchange::initCache();

// Rates will be automatically cached
$rates = Exchange::getLatestRates('usd');
```

#### Custom Cache Configuration

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

// Create a custom filesystem cache
$filesystemAdapter = new FilesystemAdapter(
    'exchange-rates',    // namespace
    3600,               // TTL (1 hour)
    '/path/to/cache'    // custom directory
);
$cache = new Psr16Cache($filesystemAdapter);

// Initialize SDK with custom cache
Exchange::initCache($cache);
```

#### Using Redis Cache

```php
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

$redis = RedisAdapter::createConnection('redis://localhost');
$cache = new Psr16Cache(new RedisAdapter($redis));

Exchange::initCache($cache, 3600); // Cache for 1 hour
```

#### Cache Management

```php
// Clear the cache
Exchange::clearCache();
```

### Custom HTTP Client Configuration

```php
use GuzzleHttp\Client;

// Configure custom Guzzle client
$client = new Client([
    'timeout' => 15,
    'connect_timeout' => 10,
    'headers' => [
        'User-Agent' => 'MyApp/1.0'
    ]
]);

Exchange::setClient($client);
```

## Available Methods

### Main Methods

- `getLatestRates(string $baseCurrency, ?string $endpoint = null): ?array`
- `getHistoricalRates(string $date, string $baseCurrency, ?string $endpoint = null): ?array`
- `getLatestRate(string $fromCurrency, string $toCurrency, ?string $endpoint = null): ?float`
- `getHistoricalRate(string $date, string $fromCurrency, string $toCurrency, ?string $endpoint = null): ?float`

### Configuration Methods

- `setPreferredEndpoint(string $endpoint): void`
- `initCache(?CacheInterface $cache = null, int $ttl = 86400): void`
- `setClient(Client $client): void`
- `clearCache(): void`
- `clearClient(): void`

## Endpoint Constants

- `Exchange::ENDPOINT_JSDELIVR` - Use JSDelivr CDN (default)
- `Exchange::ENDPOINT_CLOUDFLARE` - Use Cloudflare Pages

## Error Handling

The SDK handles various error scenarios gracefully:

```php
// All methods return null on failure
$rates = Exchange::getLatestRates('invalid_currency');
if ($rates === null) {
    // Handle error
}

// Invalid endpoint configuration throws Exception
try {
    Exchange::setPreferredEndpoint('invalid_endpoint');
} catch (Exception $e) {
    // Handle error
}
```

## Fallback Mechanism

The SDK implements a robust fallback mechanism:

1. Try preferred endpoint
2. If preferred endpoint fails, try fallback endpoint
3. Results are cached for subsequent requests
4. Cache duration is configurable

## Testing

Run the test suite with PHPUnit:

```bash
./vendor/bin/phpunit
```

## Requirements

- PHP 7.4 or higher
- Guzzle HTTP Client 7.0 or higher
- PSR-16 Simple Cache implementation

## License

MIT License. See LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

This package is a wrapper for the [Currency Exchange API](https://github.com/fawazahmed0/exchange-api) by [Fawaz Ahmed](https://github.com/fawazahmed0).