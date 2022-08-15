<?php

namespace PaywithTerra\Cache;

interface CacheInterface
{
    public function get($key);
    public function set($key, $data);
    public function delete($key);
}