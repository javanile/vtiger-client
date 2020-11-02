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
     * @var array
     */
    protected $operations;

    /**
     * OperationMapper constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->reset();

        if (array_key_exists('operationsMap', $args)) {
            if (array_key_exists('operationsMapMode', $args) && $args['operationsMapMode'] == 'set') {
                $this->apply($args['operationsMap']);
            } else {
                $this->merge($args['operationsMap']);
            }
        }
    }

    /**
     * Return the OperationsMap.
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Return the operation name.
     *
     * @param $operation
     *
     * @return mixed|null
     */
    public function get($operation)
    {
        return isset($this->operations[$operation]) ? $this->operations[$operation] : null;
    }

    /**
     * Set the OperationsMap.
     * @param $operationsMap
     *
     * @return array|void
     */
    public function apply($operations)
    {
        if (!is_array($operations)) {
            return;
        }

        return $this->operations = $operations;
    }

    /**
     * Merge the OperationsMap.
     *
     * @param $operationsMap
     */
    public function merge($operations)
    {
        if (!is_array($operations)) {
            return;
        }

        foreach (array_keys($this->operations) as $operation) {
            if (array_key_exists($operation, $operations)) {
                $this->operations[$operation] = $operations[$operation];
            }
        }
    }

    /**
     * Reset the OperationsMap to default value.
     */
    public function reset()
    {
        $this->operations = [
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
