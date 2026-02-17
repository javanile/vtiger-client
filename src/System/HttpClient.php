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

namespace Javanile\VtigerClient\System;

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
     * @var bool
     */
    protected $xdebug = false;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->endpoint = $args['endpoint'].'/webservice.php';
        $this->xdebug = !empty($args['xdebug']);

        if (isset($args['verify'])) {
            $args['guzzle']['verify'] = $args['verify'];
        }

        $this->client = new Client(isset($args['guzzle']) ? $args['guzzle'] : []);

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
    protected function decodeResponse($response)
    {
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        if (!$body && !$json) {
            return Response::error('EMPTY_RESPONSE', 'Web service send an empty body');
        }

        if ($body && !$json) {
            return Response::error('JSON_PARSE_ERROR', $body);
        }

        return $json;
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function get($request)
    {
        return $this->request('GET', $request);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function post($request)
    {
        return $this->request('POST', $request);
    }

    /**
     * @param $method
     * @param $request
     *
     * @return array|mixed
     */
    protected function request($method, $request)
    {
        if ($this->xdebug) {
            $request['query']['XDEBUG_SESSION_START'] = 'PHPSTORM';
        }

        try {
            $response = $this->client->request($method, $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return $this->logger->request($method, $request, Response::error('GUZZLE_ERROR', $error->getMessage()));
        }

        return $this->logger->request($method, $request, $this->decodeResponse($response));
    }

    /**
     *
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
