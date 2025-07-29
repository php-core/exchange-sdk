<?php

namespace PHPCore\ExchangeSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use PHPCore\ExchangeSDK\Cache\CacheManager;
use Psr\SimpleCache\CacheInterface;

class Exchange
{
    public const ENDPOINT_JSDELIVR = 'jsdelivr';
    public const ENDPOINT_CLOUDFLARE = 'cloudflare';
    
    private const API_VERSION = 'v1';
    public const SDK_VERSION = 'v1.0.1';

    private static array $baseUrls = [
        'jsdelivr' => 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@%s/' . self::API_VERSION,
        'cloudflare' => 'https://%s.currency-api.pages.dev/' . self::API_VERSION
    ];

    private static ?Client $client = null;
    private static string $preferredEndpoint = self::ENDPOINT_JSDELIVR;
    private static ?CacheManager $cache = null;

    /**
     * Initialize the cache manager
     */
    public static function initCache(?CacheInterface $cache = null, int $ttl = 86400): void
    {
        self::$cache = new CacheManager($cache, $ttl);
    }

    /**
     * Get or create the Guzzle HTTP client instance
     */
    private static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'timeout' => 10,
                'connect_timeout' => 5,
                'http_errors' => false,
                'headers' => [
                    'User-Agent' => 'ExchangeSDK/' . self::SDK_VERSION
                ]
            ]);
        }
        return self::$client;
    }

    /**
     * Get or create the cache manager
     */
    private static function getCache(): CacheManager
    {
        if (self::$cache === null) {
            self::initCache();
        }
        return self::$cache;
    }

    /**
     * Set the preferred endpoint for API calls
     */
    public static function setPreferredEndpoint(string $endpoint): void
    {
        if (!in_array($endpoint, [self::ENDPOINT_JSDELIVR, self::ENDPOINT_CLOUDFLARE])) {
            throw new Exception('Invalid endpoint specified');
        }
        self::$preferredEndpoint = $endpoint;
    }

    /**
     * Get list of all available currencies
     *
     * @param string|null $endpoint Override the default endpoint
     * @return array|null Returns list of currencies or null on failure
     */
    public static function getCurrencies(?string $endpoint = null): ?array
    {
        return self::makeRequest('/currencies', 'latest', $endpoint);
    }

    /**
     * Get the latest exchange rates for a specific currency
     *
     * @param string $baseCurrency The base currency code (e.g., 'usd', 'eur')
     * @param string|null $endpoint Override the default endpoint
     * @return array|null Returns exchange rates or null on failure
     */
    public static function getLatestRates(string $baseCurrency, ?string $endpoint = null): ?array
    {
        $baseCurrency = strtolower($baseCurrency);
        return self::makeRequest("/currencies/{$baseCurrency}", 'latest', $endpoint);
    }

    /**
     * Get historical exchange rates for a specific date and currency
     *
     * @param string $date The date in YYYY-MM-DD format
     * @param string $baseCurrency The base currency code (e.g., 'usd', 'eur')
     * @param string|null $endpoint Override the default endpoint
     * @return array|null Returns exchange rates or null on failure
     */
    public static function getHistoricalRates(string $date, string $baseCurrency, ?string $endpoint = null): ?array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('Invalid date format. Use YYYY-MM-DD');
        }

        $baseCurrency = strtolower($baseCurrency);
        return self::makeRequest("/currencies/{$baseCurrency}", $date, $endpoint);
    }

    /**
     * Get the latest exchange rate between two currencies
     *
     * @param string $fromCurrency The base currency code
     * @param string $toCurrency The target currency code
     * @param string|null $endpoint Override the default endpoint
     * @return float|null Returns the exchange rate or null on failure
     */
    public static function getLatestRate(string $fromCurrency, string $toCurrency, ?string $endpoint = null): ?float
    {
        $fromCurrency = strtolower($fromCurrency);
        $toCurrency = strtolower($toCurrency);
        
        $response = self::makeRequest("/currencies/{$fromCurrency}", 'latest', $endpoint);
        return $response[$fromCurrency][$toCurrency] ?? null;
    }

    /**
     * Get historical exchange rate between two currencies for a specific date
     *
     * @param string $date The date in YYYY-MM-DD format
     * @param string $fromCurrency The base currency code
     * @param string $toCurrency The target currency code
     * @param string|null $endpoint Override the default endpoint
     * @return float|null Returns the exchange rate or null on failure
     */
    public static function getHistoricalRate(
        string $date,
        string $fromCurrency,
        string $toCurrency,
        ?string $endpoint = null
    ): ?float {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('Invalid date format. Use YYYY-MM-DD');
        }

        $fromCurrency = strtolower($fromCurrency);
        $toCurrency = strtolower($toCurrency);
        
        $response = self::makeRequest("/currencies/{$fromCurrency}", $date, $endpoint);
        return $response[$fromCurrency][$toCurrency] ?? null;
    }

    /**
     * Make HTTP request to the API with fallback support
     *
     * @param string $path The API endpoint path
     * @param string $date The date (latest or YYYY-MM-DD)
     * @param string|null $endpoint Override the default endpoint
     * @return array|null Returns decoded JSON response or null on failure
     */
    private static function makeRequest(string $path, string $date, ?string $endpoint = null): ?array
    {
        $selectedEndpoint = $endpoint ?? self::$preferredEndpoint;
        $cache = self::getCache();
        
        // Generate cache key
        $cacheKey = $cache->getCacheKey($selectedEndpoint, $date, $path);
        
        // Try to get from cache first
        $cachedData = $cache->get($cacheKey);
        if ($cachedData !== null) {
            return $cachedData;
        }

        // If not in cache, make API request
        $client = self::getClient();
        
        // Try preferred endpoint
        $baseUrl = sprintf(self::$baseUrls[$selectedEndpoint], $date);
        $response = self::tryRequest($client, $baseUrl . $path . '.json');
        
        if ($response !== null) {
            $cache->set($cacheKey, $response);
            return $response;
        }

        // If preferred endpoint failed, try the fallback endpoint
        $fallbackEndpoint = $selectedEndpoint === self::ENDPOINT_JSDELIVR 
            ? self::ENDPOINT_CLOUDFLARE 
            : self::ENDPOINT_JSDELIVR;
            
        $fallbackUrl = sprintf(self::$baseUrls[$fallbackEndpoint], $date) . $path . '.json';
        $response = self::tryRequest($client, $fallbackUrl);
        
        if ($response !== null) {
            $cache->set($cacheKey, $response);
        }

        return $response;
    }

    /**
     * Try a single request
     *
     * @param Client $client The Guzzle client
     * @param string $url The full URL to request
     * @return array|null Returns decoded JSON response or null on failure
     */
    private static function tryRequest(Client $client, string $url): ?array
    {
        try {
            $response = $client->get($url);
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                if (is_array($data)) {
                    return $data;
                }
            }
        } catch (GuzzleException $e) {
            // Log or handle the exception if needed
        }

        return null;
    }

    /**
     * Set a custom Guzzle client instance
     */
    public static function setClient(Client $client): void
    {
        self::$client = $client;
    }

    /**
     * Clear the current Guzzle client instance
     */
    public static function clearClient(): void
    {
        self::$client = null;
    }

    /**
     * Clear the cache
     */
    public static function clearCache(): void
    {
        if (self::$cache !== null) {
            self::$cache->clear();
        }
    }
}