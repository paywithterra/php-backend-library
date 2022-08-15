<?php

namespace PaywithTerra\Cache;

use DateTime;
use Exception;
use PaywithTerra\Exception\CacheException;

/**
 * @property DateTime $expirationDate
 */
class FileCacheItem implements CacheItemInterface
{

    public $key;
    public $value;
    public $expirationDate;

    /**
     * @throws CacheException
     */
    public function __construct($key, $value, $expirationDate)
    {
        $this->key = $key;
        $this->value = $value;
        $this->expirationDate = $expirationDate;
    }

    public function serialize()
    {
        return serialize([
            'key' => $this->key,
            'value' => $this->value,
            'expirationDate' => $this->expirationDate->format('c'),
        ]);
    }

    /**
     * @throws CacheException
     */
    public static function unserialize($content)
    {
        $data = unserialize($content);

        try {
            $expirationDate = new DateTime($data['expirationDate']);
        } catch (Exception $e) {
            throw new CacheException(sprintf('Failed to unserialize cache item: %s', $e->getMessage()));
        }

        return new self($data['key'], $data['value'], $expirationDate);
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function expiresAt($expiration)
    {
        $this->expirationDate = $expiration;
    }

    public function expiresAfter($time)
    {
        $date = new DateTime();
        $date->modify(+$time);
        $this->expirationDate = $date;
    }

    public function isExpired()
    {
        return $this->expirationDate < new DateTime();
    }
}