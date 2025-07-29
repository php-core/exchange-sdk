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
$historicalRates = Exchange::getHistoricalRates('2023-07-29', 'usd');
```

## Usage Examples

### Getting Latest Exchange Rates

```php
use PHPCore\ExchangeSDK\Exchange;

// Get all rates for USD
$usdRates = Exchange::getLatestRates('usd');
// Returns: ['date' => '2023-07-29', 'usd' => ['eur' => 0.85, 'gbp' => 0.73, ...]]

// Get specific currency pair rate
$eurUsdRate = Exchange::getLatestRate('eur', 'usd');
// Returns: 1.18 (float)
```

### Getting Historical Rates

```php
// Get historical rates for a specific date
$historicalRates = Exchange::getHistoricalRates('2023-07-29', 'usd');
// Returns: ['date' => '2023-07-29', 'usd' => ['eur' => 0.84, ...]]

// Get historical rate for a specific currency pair
$historicalEurUsd = Exchange::getHistoricalRate('2023-07-29', 'eur', 'usd');
// Returns: 1.17 (float)
```

### Configuring Endpoints

```php
// Set Cloudflare as the preferred endpoint globally
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);

// Or use a specific endpoint for a single call
$rates = Exchange::getLatestRates('usd', Exchange::ENDPOINT_JSDELIVR);
```

### Advanced Usage

```php
use GuzzleHttp\Client;

// Configure custom Guzzle client
$client = new Client([
    'timeout' => 15,
    'proxy' => 'http://proxy.example.com'
]);

Exchange::setClient($client);

// Get rates with custom configuration
$rates = Exchange::getLatestRates('usd');

// Clear custom client
Exchange::clearClient();
```

## Available Methods

### Main Methods

- `getLatestRates(string $baseCurrency, ?string $endpoint = null): ?array`
- `getHistoricalRates(string $date, string $baseCurrency, ?string $endpoint = null): ?array`
- `getLatestRate(string $fromCurrency, string $toCurrency, ?string $endpoint = null): ?float`
- `getHistoricalRate(string $date, string $fromCurrency, string $toCurrency, ?string $endpoint = null): ?float`

### Configuration Methods

- `setPreferredEndpoint(string $endpoint): void`
- `setClient(Client $client): void`
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

The SDK implements a robust fallback mechanism in the following order:

1. Try preferred endpoint with minified JSON
2. Try preferred endpoint with regular JSON
3. Try fallback endpoint with minified JSON
4. Try fallback endpoint with regular JSON

This ensures maximum availability and reliability of the service.

## Testing

Run the test suite with PHPUnit:

```bash
./vendor/bin/phpunit
```

## Requirements

- PHP 7.4 or higher
- Guzzle HTTP Client 7.0 or higher

## License

MIT License. See LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

This package is a wrapper for the [Currency Exchange API](https://github.com/fawazahmed0/exchange-api) by [Fawaz Ahmed](https://github.com/fawazahmed0).