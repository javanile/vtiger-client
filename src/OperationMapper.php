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

class OperationMapper
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
        $this->resetOperationsMap();

        /*
    else {
        if (array_key_exists('operationsMap', $args)) {
            if (array_key_exists('operationsMapMode', $args) && $args['operationsMapMode'] == 'set') {
                $this->setOperationsMap($args['operationsMap']);
            } else {
                $this->mergeOperationsMap($args['operationsMap']);
            }
        }
    }
*/
    }

    /**
     * Return the OperationsMap.
     */
    public function getOperationsMap()
    {
        return $this->operationsMap;
    }

    /**
     * Set the OperationsMap.
     */
    public function setOperationsMap($operationsMap)
    {
        if (!is_array($operationsMap)) {
            return;
        }

        return $this->operationsMap = $operationsMap;
    }

    /**
     * Merge the OperationsMap.
     *
     * @param $operationsMap
     */
    public function mergeOperationsMap($operationsMap)
    {
        if (!is_array($operationsMap)) {
            return;
        }

        foreach (array_keys($this->operationsMap) as $operation) {
            if (array_key_exists($operation, $operationsMap)) {
                $this->operationsMap[$operation] = $operationsMap[$operation];
            }
        }
    }

    /**
     * Reset the OperationsMap to default value.
     */
    public function resetOperationsMap()
    {
        $this->operationsMap = [
            'getchallenge' => 'getchallenge',
            'login' => 'login',
            'listtypes' => 'listtypes',
            'describe' => 'describe',
            'create' => 'create',
            'retrieve' => 'retrieve',
            'update' => 'update',
            'delete' => 'delete',
            'query' => 'query',
            'sync' => 'sync',
        ];
    }

}
