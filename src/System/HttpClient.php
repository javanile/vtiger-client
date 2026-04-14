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
     * @var bool
     */
    protected $verbose = false;

    /**
     * @var bool
     */
    protected $debugError = false;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->endpoint = $args['endpoint'].'/webservice.php';
        $this->xdebug = !empty($args['xdebug']);
        $this->verbose = !empty($args['verbose']);
        $this->debugError = !empty($args['debug_error']);

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

        if ($this->debugError) {
            $request['query']['DEBUG_ERROR'] = 'yes';
        }

        try {
            $response = $this->client->request($method, $this->endpoint, $request);
        } catch (GuzzleException $error) {
            $errorResponse = Response::error('GUZZLE_ERROR', $error->getMessage());
            if ($this->verbose) {
                $this->verboseWrite($method, $request, $errorResponse);
            }
            return $this->logger->request($method, $request, $errorResponse);
        }

        $decoded = $this->decodeResponse($response);

        if ($this->verbose) {
            $this->verboseWrite($method, $request, $decoded);
        }

        return $this->logger->request($method, $request, $decoded);
    }

    /**
     * Write a verbose line to STDERR showing the operation and its response.
     *
     * @param string $method   HTTP method (GET/POST)
     * @param array  $request  Guzzle request params
     * @param array  $decoded  Decoded JSON response
     */
    protected function verboseWrite($method, $request, $decoded)
    {
        $operation = $request['query']['operation']
            ?? $request['form_params']['operation']
            ?? '?';

        $success = !empty($decoded['success']) ? 'OK' : 'ERROR';
        $json = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        fwrite(STDERR, sprintf("[verbose] %s %s %s %s\n", $method, $operation, $success, $json));
    }

    /**
     *
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
