<?php

/**
 * File description.
 *
 * PHP version 5
 *
 * @category   WebServiceClient
 *
 * @author     Francesco Bianco
 * @copyright
 * @license    MIT
 */

namespace Javanile\VtigerClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $logger;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->endpoint = $args['endpoint'].'/webservice.php';

        $this->client = new Client();

        $this->logger = new Logger($args);
    }

    /**
     * Decode webservice response to JSON.
     *
     * @param $response
     * @param $trace
     *
     * @return array|mixed
     */
    protected function decodeResponse($response, $trace)
    {
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        if (!$body && !$json) {
            return $this->logger->log(Response::error('EMPTY_RESPONSE', 'Web service send an empty body'), $trace);
        }

        if ($body && !$json) {
            return $this->logger->log(Response::error('JSON_PARSE_ERROR', $body), $trace);
        }

        return $this->logger->log($json, $trace);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function get($request)
    {
        $trace = [
            'method' => 'GET',
        ];

        try {
            $response = $this->client->request('GET', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return Response::error('GUZZLE_ERROR', $error->getMessage());
        }

        return $this->decodeResponse($response, $trace);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function post($request)
    {
        $trace = [
            'method' => 'POST',
        ];

        try {
            $response = $this->client->request('POST', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return Response::error('GUZZLE_ERROR', $error->getMessage());
        }

        return $this->decodeResponse($response, $trace);
    }

    /**
     *
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
