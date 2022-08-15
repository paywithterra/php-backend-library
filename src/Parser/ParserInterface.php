<?php

namespace PaywithTerra\Parser;

use PaywithTerra\Entity\NormalTx;
use PaywithTerra\Exception\TxParsingException;


interface ParserInterface
{
    /**
     * @param $data
     * @throws TxParsingException
     * @return NormalTx
     */
    public function extractNormalTx($data);
}