<?php

namespace PaywithTerra\Cache;

interface CacheItemInterface
{
    public function get();
    public function set($value);
    public function expiresAt($expiration);
    public function expiresAfter($time);
    public function isExpired();
}