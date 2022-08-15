<?php

namespace PaywithTerra;

use Exception;
use PaywithTerra\Cache\CacheInterface;
use PaywithTerra\Entity\NormalTx;
use PaywithTerra\Exception\TxValidationException;
use PaywithTerra\Parser\ParserFactory;
use PaywithTerra\Utils\Arr;
use PaywithTerra\Utils\HttpClient;
use RuntimeException;

/**
 * @property CacheInterface $cache
 */
class TerraTxValidator
{
    const VERSION = '1.0.0';

    private $merchantAddress = '';
    private $networkName = 'mainnet'; // testnet, mainnet, classic, localterra
    private $FCDUrl = null;
    private $LCDUrl = null;
    private $chainId = null;
    private $chainsJsonUrl = 'https://assets.terra.money/chains.json';

    private $lastLoadedTxInfo = null;

    private $workingApiMode = null; // fcd, lcd
    private $txParser;

    private $httpClient;

    private $cache;

    /**
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
        $this->httpClient = new HttpClient(Arr::get($config, 'curlOptions', []));
        if(Arr::get($config, 'cache') !== null){
            $this->cache = Arr::get($config, 'cache');
        }
        $this->loadChainsInfoIfNeeded();
        $this->detectApiMode();
        $this->initTxParser();
    }

    /**
     * @param $txHash
     * @return $this
     * @throws TxValidationException
     * @throws Exception
     */
    public function lookupTx($txHash)
    {
        if (empty($txHash)) {
            throw new TxValidationException("Transaction hash is empty");
        }

        $txInfo = $this->getTxInfoFromFacade($txHash);

        if ($txInfo === null) {
            throw new TxValidationException("Transaction not found");
        }

        $this->lastLoadedTxInfo = $this->txParser->extractNormalTx($txInfo);

        return $this;
    }

    /**
     * @param $orderData
     * @return bool
     * @throws TxValidationException
     */
    public function assertTx($orderData = [])
    {
        if (empty($this->getLastLoadedTxInfo())) {
            throw new TxValidationException("Transaction was not loaded");
        }

        $txInfo = $this->getLastLoadedTxInfo();

        $txInfo->assertProp('code', 0);
        $txInfo->assertProp('memo', Arr::get($orderData, 'memo', ''));

        $orderAmount = (int) Arr::get($orderData, 'amount', '0');
        $orderDenom = Arr::get($orderData, 'denom', '');

        if ($txInfo->inTotal($orderDenom, $this->merchantAddress) < $orderAmount) {
            throw new TxValidationException("Transaction found but no eligible payment message found or amount is less than expected");
        }

        return true;
    }

    /**
     * @param $txHash
     * @return array|null
     * @throws Exception
     */
    public function getTxInfoFromFacade($txHash)
    {
        $baseUrl = ($this->workingApiMode == 'fcd') ? $this->FCDUrl : $this->LCDUrl;

        $fullUrl = str_replace('{txHash}', $txHash, $baseUrl);

        return $this->httpClient->get($fullUrl);
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function loadChainsInfo()
    {
        if($this->cache){
            $data = $this->cache->get('chains');

            if($data){
                return $data;
            }
        }

        $data = $this->httpClient->get($this->chainsJsonUrl);
        if($this->cache){
            $this->cache->set('chains', $data);
        }

        return $data;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig($config = [])
    {
        $this->merchantAddress = Arr::get($config, 'merchantAddress');
        $this->networkName = Arr::get($config, 'networkName', 'mainnet');
        $this->chainId = Arr::get($config, 'chainId');
        $this->FCDUrl = Arr::get($config, 'FCD');
        $this->LCDUrl = Arr::get($config, 'LCD');

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function loadChainsInfoIfNeeded()
    {
        if(! $this->chainId || (! $this->LCDUrl && ! $this->FCDUrl)) {
            $info = $this->loadChainsInfo();
            $this->chainId = Arr::get($info, $this->networkName . '.chainID');
            $this->LCDUrl = Arr::get($info, $this->networkName . '.lcd') . '/cosmos/tx/v1beta1/txs/{txHash}';
        }
    }

    /**
     * @return ?NormalTx
     */
    public function getLastLoadedTxInfo()
    {
        return $this->lastLoadedTxInfo;
    }

    public function setLastLoadedTxInfo(NormalTx $txInfo)
    {
        $this->lastLoadedTxInfo = $txInfo;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    private function detectApiMode()
    {
        if(!$this->workingApiMode && $this->FCDUrl) {
            $this->workingApiMode = 'fcd';
        }
        if(!$this->workingApiMode && $this->LCDUrl) {
            $this->workingApiMode = 'lcd';
        }
    }

    private function initTxParser()
    {
        switch($this->workingApiMode) {
            case 'fcd':
                $this->txParser = ParserFactory::create(ParserFactory::TYPE_FCD);
                break;
            case 'lcd':
                $this->txParser = ParserFactory::create(ParserFactory::TYPE_LCD);
                break;
            default:
                throw new RuntimeException("Unknown API mode: " . $this->workingApiMode);
        }
    }
}