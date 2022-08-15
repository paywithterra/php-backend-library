<?php


use PaywithTerra\TerraTxValidator;
use PHPUnit\Framework\TestCase;

class TerraTxValidatorTest extends TestCase
{

    public function testAssertTx()
    {
        $client = new TerraTxValidator([
            "chainId" => "test-1",
            "merchantAddress" => "terra321321",
        ]);

        $client->setLastLoadedTxInfo(new \PaywithTerra\Entity\NormalTx([
            'memo' => 'TestMemo123',
            'code' => 0,
            'txhash' => 'mytesthash123',
            'messages' => [
                [
                    'type' => '/cosmos.bank.v1beta1.MsgSend',
                    'amounts' => [
                        [
                            'amount' => '123000',
                            'denom' => 'test-1',
                        ],
                    ],
                    'from' => 'terra123123',
                    'to' => 'terra321321',
                ],
            ],
        ]));

        $this->assertTrue(
            $client->assertTx([
                "memo" => 'TestMemo123',
                "denom" => "test-1",
                "amount" => "123000",
            ])
        );
    }
}
