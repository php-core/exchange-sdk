<?php

namespace PHPCore\ExchangeSDK\Cache;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class CacheManager implements CacheInterface
{
    private CacheInterface $cache;
    private int $ttl;

    public function __construct(?CacheInterface $cache = null, int $ttl = 86400) // 24 hours default
    {
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(
            namespace: 'exchange-sdk',
            defaultLifetime: $ttl,
            directory: sys_get_temp_dir() . '/exchange-sdk-cache'
        ));
        $this->ttl = $ttl;
    }

    public function get($key, $default = null)
    {
        return $this->cache->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($key, $value, $ttl ?? $this->ttl);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function clear()
    {
        return $this->cache->clear();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->cache->getMultiple($keys, $default);
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->cache->setMultiple($values, $ttl ?? $this->ttl);
    }

    public function deleteMultiple($keys)
    {
        return $this->cache->deleteMultiple($keys);
    }

    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * Generate a cache key for exchange rates
     */
    public function getCacheKey(string $endpoint, string $date, string $currency): string
    {
        return str_replace('/', '', "exchange_rate_{$endpoint}_{$date}_{$currency}");
    }
}