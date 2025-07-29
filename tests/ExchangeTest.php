<?php

namespace PHPCore\ExchangeSDK\Tests;

use PHPCore\ExchangeSDK\Exchange;
use PHPUnit\Framework\TestCase;
use Exception;

class ExchangeTest extends TestCase
{
    private const TEST_DATE = '2024-03-06';
    private const KNOWN_EUR_USD_RATE = 1.08854773;

    protected function setUp(): void
    {
        parent::setUp();
        // Use Cloudflare endpoint by default as it's more reliable
        Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);
    }

    public function testGetLatestRates()
    {
        $rates = Exchange::getLatestRates('eur');
        $this->assertIsArray($rates);
        $this->assertArrayHasKey('date', $rates);
        $this->assertArrayHasKey('eur', $rates);
        $this->assertIsArray($rates['eur']);
    }

    public function testGetHistoricalRates()
    {
        $rates = Exchange::getHistoricalRates(self::TEST_DATE, 'eur');
        $this->assertIsArray($rates);
        $this->assertArrayHasKey('date', $rates);
        $this->assertEquals(self::TEST_DATE, $rates['date']);
        $this->assertArrayHasKey('eur', $rates);
        $this->assertIsArray($rates['eur']);
    }

    public function testGetHistoricalRateForEurUsd()
    {
        $rate = Exchange::getHistoricalRate(self::TEST_DATE, 'eur', 'usd');
        $this->assertEquals(self::KNOWN_EUR_USD_RATE, $rate, '', 0.00000001);
    }

    public function testGetCurrencies()
    {
        $currencies = Exchange::getCurrencies();
        $this->assertIsArray($currencies);
        $this->assertNotEmpty($currencies);
    }

    public function testInvalidDate()
    {
        $this->expectException(Exception::class);
        Exchange::getHistoricalRates('invalid-date', 'eur');
    }

    public function testInvalidEndpoint()
    {
        $this->expectException(Exception::class);
        Exchange::setPreferredEndpoint('invalid');
    }

    public function testEndpointAvailability()
    {
        // Test that both endpoints return valid data structure
        Exchange::setPreferredEndpoint(Exchange::ENDPOINT_JSDELIVR);
        $ratesJsdelivr = Exchange::getHistoricalRates(self::TEST_DATE, 'eur');
        $this->assertIsArray($ratesJsdelivr);
        $this->assertArrayHasKey('eur', $ratesJsdelivr);
        $this->assertArrayHasKey('usd', $ratesJsdelivr['eur']);
        $this->assertIsFloat($ratesJsdelivr['eur']['usd']);

        Exchange::setPreferredEndpoint(Exchange::ENDPOINT_CLOUDFLARE);
        $ratesCloudflare = Exchange::getHistoricalRates(self::TEST_DATE, 'eur');
        $this->assertIsArray($ratesCloudflare);
        $this->assertArrayHasKey('eur', $ratesCloudflare);
        $this->assertArrayHasKey('usd', $ratesCloudflare['eur']);
        $this->assertIsFloat($ratesCloudflare['eur']['usd']);
    }

    public function testHistoricalRatesConsistency()
    {
        $fullRates = Exchange::getHistoricalRates(self::TEST_DATE, 'eur');
        $singleRate = Exchange::getHistoricalRate(self::TEST_DATE, 'eur', 'usd');

        $this->assertEquals($fullRates['eur']['usd'], $singleRate);
    }

    public function testNonExistentCurrency()
    {
        $rate = Exchange::getHistoricalRate(self::TEST_DATE, 'eur', 'nonexistent');
        $this->assertNull($rate);
    }

    public function testFutureDateReturnsNull()
    {
        $futureDate = date('Y-m-d', strtotime('+1 year'));
        $rate = Exchange::getHistoricalRate($futureDate, 'eur', 'usd');
        $this->assertNull($rate);
    }
}