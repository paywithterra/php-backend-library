<?php

namespace Cache;

use PaywithTerra\Cache\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{

    public function testCache()
    {
        $cache = new FileCache();
        $cache->set('testKey', 'value');

        $this->assertEquals('value', $cache->get('testKey'));

        $cache->delete('testKey');

        $this->assertNull($cache->get('testKey'));
    }
}
