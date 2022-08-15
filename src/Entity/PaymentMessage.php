<?php

namespace PaywithTerra\Entity;

/**
 * @property string $type
 * @property string $from
 * @property string $to
 * @property PaymentDenomAmount[] $amounts
 */
class PaymentMessage
{
    use TxValidationTrait;

    public $type;
    public $from;
    public $to;
    public $amounts;

    public function __construct($data)
    {
        $this->type = $data['type'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->amounts = [];
        foreach ($data['amounts'] as $amount) {
            $this->amounts[] = new PaymentDenomAmount($amount);
        }
    }
}