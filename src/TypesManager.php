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
     *
     */
    protected $client;

    /**
     * @var
     */
    protected $types;

    /**
     * @var
     */
    protected $typesTable;

    /**
     * @var
     */
    protected $typesResolver;

    /**
     * @var
     */
    protected $typesPriority;

    /**
     *
     */
    protected $idPrefixResolver;

    /**
     *
     */
    protected $listTypesResult;

    /**
     * @param $args
     * @param $client
     */
    public function __construct($args, $client)
    {
        $this->client = $client;

        $this->typesPriority = [
            'Accounts' => 10,
            'Currency' => 100,
            'Vendors' => 400,
            'Products' => 500,
            'Quotes' => 600,
            'SalesOrder' => 620,
            'Invoice' => 700,
            'PurchaseOrder' => 720,
            'Project' => 1000,
        ];
    }

    /**
     * @return bool
     */
    public function hasTypes()
    {
        return null !== $this->types;
    }

    /**
     * @param $listTypesResult
     */
    public function setTypes($listTypesResult)
    {
        $this->listTypesResult = $listTypesResult;
    }

    /**
     *
     */
    protected function buildTypes()
    {
        $types = isset($this->listTypesResult['types']) ? $this->listTypesResult['types'] : null;

        if (!is_array($types) || !$types) {
            return;
        }

        $this->typesTable = [];
        $this->typesResolver = [];

        foreach ($types as $type) {
            if (empty($this->listTypesResult['information'][$type])) {
                continue;
            }
            $idPrefix = $this->getIdPrefixByType($type);
            if (empty($idPrefix)) {
                continue;
            }
            $this->typesResolver[$idPrefix] = $type;
            $this->listTypesResult['information'][$type]['idPrefix'] = $idPrefix;
            $this->typesTable[$type] = $this->listTypesResult['information'][$type];
        }
        $this->types = $this->sort($types);
    }

    /**
     *
     */
    public function getTypes()
    {
        if ($this->types === null && isset($this->listTypesResult)) {
            $this->buildTypes();
        }

        return $this->types;
    }

    /**
     * @param $id
     *
     * @return mixed|null
     */
    public function getTypeByElementId($id)
    {
        if (!Functions::isElementId($id)) {
            return null;
        }

        if ($this->typesResolver === null) {
            $this->buildTypes();
        }

        $idPrefix = Functions::getTypeIdPrefix($id);

        return isset($this->typesResolver[$idPrefix]) ? $this->typesResolver[$idPrefix] : null;
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

    /**
     * @param $type
     */
    public function getIdPrefixByType($type)
    {
        if (isset($this->idPrefixResolver[$type])) {
            return $this->idPrefixResolver[$type];
        }

        $describe = $this->client->describe($type);
        if (empty($describe['success'])) {
            return;
        }
        
        $idPrefix = $describe['result']['idPrefix'];
        $this->idPrefixResolver[$type] = $idPrefix;

        return $idPrefix;
    }
}
