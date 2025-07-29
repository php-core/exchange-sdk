<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPCore\ExchangeSDK\Exchange;

// Set to Cloudflare endpoint for testing
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);

// Example 1: Get list of all available currencies
echo "Getting available currencies...\n";
$currencies = Exchange::getCurrencies();
echo "Number of available currencies: " . count($currencies) . "\n\n";

// Example 2: Get latest USD rates
echo "Getting latest USD rates...\n";
$usdRates = Exchange::getLatestRates('usd');
echo "Full response:\n";
print_r($usdRates);
echo "\n";

// Example 3: Get specific currency pair rate
echo "Getting EUR/USD rate...\n";
$eurUsdRate = Exchange::getLatestRate('eur', 'usd');
if ($eurUsdRate !== null) {
    echo "1 EUR = {$eurUsdRate} USD\n\n";
} else {
    echo "Failed to get EUR/USD rate\n\n";
}

// Example 4: Get historical rates for 2024-03-06
$date = '2024-03-06';
echo "Getting historical EUR rates for {$date}...\n";
$historicalRates = Exchange::getHistoricalRates($date, 'eur');
echo "Full response:\n";
print_r($historicalRates);
echo "\n";

// Example 5: Historical rate for specific currency pair
echo "Getting historical EUR/USD rate for {$date}...\n";
$historicalEurUsd = Exchange::getHistoricalRate($date, 'eur', 'usd');
if ($historicalEurUsd !== null) {
    echo "1 EUR = {$historicalEurUsd} USD on {$date}\n\n";
} else {
    echo "Failed to get historical EUR/USD rate\n\n";
}

// Reset to default endpoint
Exchange::setPreferredEndpoint(Exchange::ENDPOINT_JSDELIVR);