<?php

namespace PaywithTerra\Entity;

/**
 * @property string $denom
 * @property string $amount
 */
class PaymentDenomAmount
{
    public $denom;
    public $amount;

    public function __construct($data)
    {
        $this->denom = $data['denom'];
        $this->amount = $data['amount'];
    }
}