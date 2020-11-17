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
    public function __construct($args)
    {
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
        $types = isset($listTypesResult['types']) ? $listTypesResult['types'] : null;

        if (is_array($types)) {
            $idPrefix = 1;
            $this->typesTable = [];
            $this->typesResolver = [];
            foreach ($types as $type) {
                if (isset($listTypesResult['information'][$type])) {
                    $listTypesResult['information'][$type]['idPrefix'] = $idPrefix;
                    $this->typesTable[$type] = $listTypesResult['information'][$type];
                }
                $this->typesResolver[$idPrefix] = $type;
                $idPrefix++;
            }
            $this->types = $this->sort($types);
        }
    }

    /**
     *
     */
    public function getTypes()
    {
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
}
