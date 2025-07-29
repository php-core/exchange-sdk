<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPCore\ExchangeSDK\Exchange;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

// Example 1: Custom HTTP Client Configuration
echo "Example 1: Custom HTTP Client Configuration\n";
$customClient = new Client([
    'timeout' => 15,
    'connect_timeout' => 10,
    'headers' => [
        'User-Agent' => 'ExchangeSDK/' . Exchange::SDK_VERSION
    ]
]);

Exchange::setClient($customClient);

// Example 2: Custom Cache Configuration
echo "\nExample 2: Custom Cache Configuration\n";

// Create a filesystem cache with custom settings
$filesystemAdapter = new FilesystemAdapter(
    // Cache namespace
    'exchange-rates',
    // TTL - 1 hour
    3600,
    // Custom cache directory
    __DIR__ . '/../var/cache'
);

// Convert to PSR-16 SimpleCache
$cache = new Psr16Cache($filesystemAdapter);

// Initialize SDK with custom cache
Exchange::initCache($cache);

// Example 3: Using the configured SDK
echo "\nExample 3: Using the configured SDK\n";

// First call - will cache the result
echo "First call (will hit API)...\n";
$rate1 = Exchange::getLatestRate('eur', 'usd');
echo "EUR/USD Rate: " . $rate1 . "\n";

// Second call - will use cache
echo "\nSecond call (should use cache)...\n";
$rate2 = Exchange::getLatestRate('eur', 'usd');
echo "EUR/USD Rate: " . $rate2 . "\n";

// Example 4: Historical rates with cache
echo "\nExample 4: Historical rates with cache\n";
$date = '2024-03-06';
echo "Getting historical EUR/USD rate for {$date}...\n";

// Switch to Cloudflare endpoint for historical data
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);

// First call - will cache
$historicalRate1 = Exchange::getHistoricalRate($date, 'eur', 'usd');
echo "Historical Rate (API call): " . $historicalRate1 . "\n";

// Second call - will use cache
$historicalRate2 = Exchange::getHistoricalRate($date, 'eur', 'usd');
echo "Historical Rate (from cache): " . $historicalRate2 . "\n";

// Example 5: Clear cache and client
echo "\nExample 5: Clearing cache and client\n";
Exchange::clearCache();
Exchange::clearClient();

// Reset to default configuration
echo "Resetting to default configuration...\n";
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_JSDELIVR);
$rate3 = Exchange::getLatestRate('eur', 'usd');
echo "EUR/USD Rate with default config: " . $rate3 . "\n";