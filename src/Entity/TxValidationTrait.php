<?php

namespace PaywithTerra\Entity;

use PaywithTerra\Exception\TxValidationException;

trait TxValidationTrait
{
    /**
     * @throws TxValidationException
     */
    public function assertProp($key, $neededValue)
    {
        $actualValue = $this->$key;
        if ($actualValue !== $neededValue) {
            $msg = "Transaction $key is invalid ('$neededValue' needed)";

            throw new TxValidationException($msg);
        }
    }
}