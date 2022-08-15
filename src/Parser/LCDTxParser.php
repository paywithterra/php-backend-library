<?php

namespace PaywithTerra\Parser;

use PaywithTerra\Entity\NormalTx;
use PaywithTerra\Utils\Arr;

class LCDTxParser implements ParserInterface
{

    /**
     * @inheritDoc
     */
    public function extractNormalTx($data)
    {
        $paymentMessagesRaw = Arr::get($data, 'tx.body.messages', []);
        $paymentMessages = [];
        foreach ($paymentMessagesRaw as $paymentMessageRaw) {

            $amountsRaw = Arr::get($paymentMessageRaw, 'amount', []);
            $amounts = [];
            foreach ($amountsRaw as $amountRaw) {
                $amounts[] = [
                    'amount' => Arr::get($amountRaw, 'amount'),
                    'denom' => Arr::get($amountRaw, 'denom'),
                ];
            }

            $paymentMessages[] = [
                'type' => Arr::get($paymentMessageRaw, '@type'),
                'from' => Arr::get($paymentMessageRaw, 'from_address'),
                'to' => Arr::get($paymentMessageRaw, 'to_address'),
                'amounts' => $amounts,
            ];
        }
        return new NormalTx([
            'memo' => Arr::get($data, 'tx.body.memo', ''),
            'code' => Arr::get($data, 'tx_response.code', -1),
            'txhash' => Arr::get($data, 'tx_response.txhash', ''),
            'messages' => $paymentMessages,
        ]);
    }
}