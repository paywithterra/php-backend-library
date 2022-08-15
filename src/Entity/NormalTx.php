<?php

namespace PaywithTerra\Entity;

/**
 * @property string $code
 * @property string $memo
 * @property string $txhash
 * @property PaymentMessage[] $messages
 */
class NormalTx
{
    use TxValidationTrait;

    private $txMessagesValuedTypes = ["/cosmos.bank.v1beta1.MsgSend"];

    public $code;
    public $memo;
    public $txhash;
    public $messages;

    public function __construct($data)
    {
        $this->code = $data['code'];
        $this->memo = $data['memo'];
        $this->txhash = $data['txhash'];
        $this->messages = [];
        foreach ($data['messages'] as $message) {
            $this->messages[] = new PaymentMessage($message);
        }
    }

    public function inTotal($denom, $merchantAddress = null)
    {
        $total = 0;
        foreach ($this->messages as $message) {
            if(! in_array($message->type, $this->txMessagesValuedTypes)) {
                continue;
            }
            if ($merchantAddress !== null && $message->to !== $merchantAddress) {
                continue;
            }
            foreach ($message->amounts as $amount) {
                if ($amount->denom === $denom) {
                    $total += (int) $amount->amount;
                }
            }
        }
        return $total;
    }

}