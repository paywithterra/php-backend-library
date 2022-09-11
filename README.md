# PaywithTerra Backend PHP Library
PHP library for validating payment transactions on Terra blockchain.

## Prerequisites
PHP version 5.6, 7.0, 7.1, 7.2, 7.3, or 7.4  
PHP extensions: ext-json, ext-curl


## Installation

You can use [Composer](https://getcomposer.org/). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

~~~~ bash
composer require paywithterra/php-backend-library
~~~~

In your PHP script, make sure you include the Composer's autoloader:

~~~~ php
require __DIR__ . '/vendor/autoload.php';
~~~~

**Alternatively**, when you are not using Composer, you can download the [release on GitHub](https://github.com/paywithterra/php-backend-library/releases) 
and use library directly:
~~~~ php
require __DIR__ . '/src/autoload-legacy.php';
~~~~

## Using the library

### Request and validate transaction
~~~~ php
// Prepare client (see all options below)
$client = new \PaywithTerra\TerraTxValidator([
    "merchantAddress" => "terra12abcdefg1234512345123451234512345abc",
    "chainId" => "pisco-1",
    "LCD" => "https://pisco-lcd.terra.dev/cosmos/tx/v1beta1/txs/{txHash}",
]);

// Request transaction information from public facade (by txHash)
$client->lookupTx("TynpStpmcovsgsNpqlchimy9cydlc83bwudgrBigShTksrzczfvxnf9q4kkvcek4");

// Check if transaction comply with our requirements (throw exception if not)
$client->assertTx([
    "memo" => '#order-1234',
    "denom" => "uluna",
    "amount" => "280000",
]);
~~~~

## Client options (initialization variants)

### Variant 1: Using exact LCD and chainId
~~~~ php
$client = new \PaywithTerra\TerraTxValidator([
    "merchantAddress" => "terra12abcdefg1234512345123451234512345abc",
    "chainId" => "pisco-1",
    "LCD" => "https://pisco-lcd.terra.dev/cosmos/tx/v1beta1/txs/{txHash}",
]);
~~~~

### Variant 2: Using exact FCD and chainId
~~~~ php
$client = new \PaywithTerra\TerraTxValidator([
    "merchantAddress" => "terra12abcdefg1234512345123451234512345abc",
    "chainId" => "pisco-1",
    "FCD" => "https://pisco-fcd.terra.dev/v1/tx/{txHash}",
]);
~~~~

### Variant 3: Using network name (requests chains info + LCD url from Terra network and cache it)
~~~~ php
$client = new \PaywithTerra\TerraTxValidator([
    "merchantAddress" => "terra12abcdefg1234512345123451234512345abc",
    "networkName" => "testnet", // or "mainnet" or "classic"
    "cache" => new \PaywithTerra\Cache\FileCache(),
]);
~~~~

## Assets (denoms) support

| Denom code                                                             | Asset name / Description                                                                                                  |
|------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------|
| `uluna`                                                                | **Luna** <br/> Default (basic) network asset                                                                              |
| `ibc/B3504E092456BA618CC28AC671A71FB08C6CA0FD0BE7C8A5B5A3E2DD933CC9E4` | axl**USDC** (mainnet) <br/> The USDC representation in Terra network (can be swapped from Luna right on Station Mobile)   |
| `ibc/CBF67A2BCF6CAE343FDF251E510C8E18C361FC02B23430C121116E0811835DEF` | axl**USDT** (mainnet) <br/> The Tether representation in Terra network (can be swapped from Luna right on Station Mobile) |

## Notes
We don't use any of modern libraries (like Guzzle or Symphony HTTP Client) as transport 
because we want to keep the minimal supported PHP version as low as possible.  
This approach allows even old-school and legacy PHP-projects to connect to Terra.
If you really want to use HTTP clients as transport, please open an issue.


## License
[The MIT License (MIT)](LICENSE)
