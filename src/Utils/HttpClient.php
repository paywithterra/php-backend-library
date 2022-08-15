<?php

namespace PaywithTerra\Utils;

use Exception;
use PaywithTerra\TerraTxValidator;

/**
 * @property array $curlOptions
 * @property int $lastResponseCode
 * @property array $lastResponseHeaders
 */
class HttpClient
{
    private $curlOptions;
    private $lastResponseCode;
    private $lastResponseHeaders;

    public function __construct($curlOptions = [])
    {
        $this->curlOptions = $curlOptions;
    }

    public function get($url)
    {
        $httpHeaders = [
            'User-Agent: PaywithTerraClient_PHP/' . TerraTxValidator::VERSION
        ];

        $session = curl_init($url);

        $options = $this->createCurlOptions($httpHeaders);

        curl_setopt_array($session, $options);
        $content = curl_exec($session);

        $responseCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
        if ($responseCode !== 200) {
            return null;
        }

        if ($content === false) {
            throw new Exception(curl_error($session), curl_errno($session));
        }

        $responseBody = $this->parseResponse($session, $content);

        curl_close($session);

        $parsedBody = @json_decode($responseBody, true);


        if (null === $parsedBody) {
            $msg = "Unable to parse API response";
            throw new Exception($msg);
        }

        return $parsedBody;
    }

    /**
     * Creates curl options for a request
     * this function does not mutate any private variables.
     *
     * @param null $addHeaders
     * @return array
     */
    private function createCurlOptions($addHeaders = null)
    {
        $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_CUSTOMREQUEST => strtoupper('GET'),
                CURLOPT_FAILONERROR => false,
            ] + $this->curlOptions;

        $headers = [];

        if (isset($addHeaders)) {
            $headers = array_merge($headers, $addHeaders);
        }

        if (isset($body)) {
            $encodedBody = json_encode($body);
            $options[CURLOPT_POSTFIELDS] = $encodedBody;
            $headers = array_merge($headers, ['Content-Type: application/json']);
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        return $options;
    }


    private function parseResponse($session, $content)
    {
        $headerSize = curl_getinfo($session, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

        $responseBody = substr($content, $headerSize);

        $responseHeaders = substr($content, 0, $headerSize);
        $responseHeaders = explode("\n", $responseHeaders);
        $responseHeaders = array_map('trim', $responseHeaders);

        $this->lastResponseCode = $statusCode;
        $this->lastResponseHeaders = $responseHeaders;

        return $responseBody;
    }

    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }

    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }
}