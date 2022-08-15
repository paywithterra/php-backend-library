<?php

namespace PaywithTerra\Cache;

use DateTime;
use PaywithTerra\Exception\CacheException;
use PaywithTerra\Utils\Arr;

class FileCache implements CacheInterface
{
    private $cacheDir;

    public function __construct($config = [])
    {
        $this->cacheDir = Arr::get($config, 'cacheDir', sys_get_temp_dir());

        if (!is_dir($this->cacheDir) || !is_writable($this->cacheDir)) {
            throw new CacheException(sprintf('Cache directory "%s" is not exists or non writable', $this->cacheDir));
        }
    }

    /**
     * @throws CacheException
     */
    public function get($key)
    {
        file_exists($this->getFileName($key)) ? $data = file_get_contents($this->getFileName($key)) : $data = null;
        if (empty($data)) {
            return null;
        }

        $cacheItem = FileCacheItem::unserialize($data);
        if($cacheItem->isExpired()){
            return null;
        }

        return $cacheItem->get();
    }

    /**
     * @throws CacheException
     */
    public function set($key, $data)
    {
        $item = new FileCacheItem($key, $data, new DateTime('+1 hour'));
        $content = $item->serialize();
        file_put_contents($this->getFileName($key), $content);
    }

    public function delete($key)
    {
        unlink($this->getFileName($key));
    }

    protected function getFileName($key)
    {
        return $this->cacheDir . '/' . $key . '.cache';
    }
}