<?php

namespace PaywithTerra\Parser;

class ParserFactory
{
    const TYPE_FCD = 'fcd';
    const TYPE_LCD = 'lcd';

    public static function create($type)
    {
        switch ($type) {
            case self::TYPE_FCD:
                return new FCDTxParser();
            case self::TYPE_LCD:
                return new LCDTxParser();
            default:
                throw new \InvalidArgumentException('Unknown parser type: ' . $type);
        }
    }

}