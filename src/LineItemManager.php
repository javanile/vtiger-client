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

class LineItemManager
{
    /**
     * @var
     */
    protected $client;

    /**
     *
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $element
     *
     * @return int
     */
    public function update($element)
    {
        $lineItemId = $element['id'];
        $retrieveResponse = $this->client->retrieve($lineItemId);
        if (empty($retrieveResponse['success'])) {
            return Response::error('LINE_ITEM_NOT_EXISTS', 'Line item not exists and not updated');
        }

        $element = array_merge($retrieveResponse['result'], $element);
        unset($element['id']);

        $createResponse = $this->client->create('LineItem', $element);
        if (empty($createResponse['success'])) {
            return Response::error('LINE_ITEM_ERROR', 'Error in updating line item');
        }

        $deleteResponse = $this->client->delete($lineItemId);
        if (empty($deleteResponse['success'])) {
            $this->client->delete($createResponse['id']);
            return Response::error('LINE_ITEM_ERROR', 'Error in updating line item');
        }

        return $createResponse;
    }
}
