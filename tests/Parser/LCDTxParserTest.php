<?php

namespace Parser;

use PaywithTerra\Entity\PaymentDenomAmount;
use PaywithTerra\Exception\TxParsingException;
use PaywithTerra\Parser\LCDTxParser;
use PHPUnit\Framework\TestCase;

class LCDTxParserTest extends TestCase
{
    /** @noinspection PhpParamsInspection */
    /**
     * @throws TxParsingException
     */
    public function testExtractNormalTx()
    {
        $parser = new LCDTxParser();
        $txNormal = $parser->extractNormalTx([
            'tx' => [
                'body' => [
                    'messages' => [
                        [
                            '@type' => '/cosmos.bank.v1beta1.MsgSend',
                            'amount' => [
                                [
                                    'amount' => '123000',
                                    'denom' => 'myTestDenom',
                                ],
                            ],
                            'from_address' => 'terra123456',
                            'to_address' => 'terra78910112',
                        ],
                    ],
                    'memo' => 'TestMemo123',
                ]
            ],
            'tx_response' => [
                'code' => 0,
                'txhash'  => 'myTxHash123',
            ]
        ]);

        $this->assertEquals(0, $txNormal->code);
        $this->assertEquals('TestMemo123', $txNormal->memo);
        $this->assertEquals('myTxHash123', $txNormal->txhash);
        $this->assertCount(1, $txNormal->messages);
        $this->assertEquals(new PaymentDenomAmount([
            'amount' => '123000',
            'denom' => 'myTestDenom',
        ]), $txNormal->messages[0]->amounts[0]);
    }
}
