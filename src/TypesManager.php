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

class TypesManager
{
    /**
     * @var
     */
    protected $typesPriority;

    /**
     *
     */
    public function __construct($args)
    {
        $this->typesPriority = [
            'Accounts' => 10,
            'Products' => 500,
            'Project' => 1000,
        ];
    }

    /**
     * @param $types
     *
     * @return mixed
     */
    public function sort($types)
    {
        usort($types, [$this, 'sortFunction']);

        return $types;
    }

    /**
     * @param $leftType
     * @param $rightType
     * @return bool
     */
    public function sortFunction($leftType, $rightType)
    {
        return $this->getTypePriority($leftType) > $this->getTypePriority($rightType);
    }

    /**
     * @param $type
     * @return int
     */
    public function getTypePriority($type)
    {
        if (empty($this->typesPriority[$type])) {
            return 5000;
        }

        return $this->typesPriority[$type];
    }
}
