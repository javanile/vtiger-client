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
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->endpoint = $args['endpoint'].'/webservice.php';

        $this->client = new Client();
    }

    /**
     * Decode webservice response to JSON.
     *
     * @param $response
     *
     * @return array|mixed
     */
    protected function decodeResponse($response)
    {
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        if (!$body && !$json) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'EMPTY_RESPONSE',
                    'message' => 'Web service send an empty body',
                ],
            ];
        }

        if ($body && !$json) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'JSON_PARSE_ERROR',
                    'message' => $body,
                ],
            ];
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
        try {
            $response = $this->client->request('GET', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'GUZZLE_ERROR',
                    'message' => $error->getMessage(),
                ],
            ];
        }

        return $this->decodeResponse($response);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    public function post($request)
    {
        try {
            $response = $this->client->request('POST', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'GUZZLE_ERROR',
                    'message' => $error->getMessage(),
                ],
            ];
        }

        return $this->decodeResponse($response);
    }
}
