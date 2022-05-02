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

class ElementSanitizer
{
    /**
     * @var string
     */
    protected $defaultAssignedUserId;

    /**
     * @var string
     */
    protected $inventoryTypes;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->inventoryTypes = ['Quotes', 'SalesOrder', 'Invoice', 'PurchaseOrder'];
    }

    /**
     * @param $userId
     */
    public function setDefaultAssignedUserId($userId)
    {
        $this->defaultAssignedUserId = $userId;
    }

    /**
     * @param $element
     *
     * @return mixed
     */
    protected function sanitizeAssignedUserId($element)
    {
        if (empty($element['assigned_user_id']) && $this->defaultAssignedUserId) {
            $element['assigned_user_id'] = $this->defaultAssignedUserId;
        }

        return $element;
    }

    /**
     * @param $element
     *
     * @return array
     */
    protected function sanitizeEmptyElement($element)
    {
        if (empty($element)) {
            $element = [];
        }

        return $element;
    }

    /**
     * Sanitize an element for inventory modules.
     *
     * @param $operation
     * @param $elementType
     * @param $element
     *
     * @return array
     */
    protected function sanitizeInventoryElement($operation, $elementType, $element)
    {
        if (!in_array($elementType, $this->inventoryTypes)) {
            return $element;
        }

        if (empty($element['hdnTaxType'])) {
            $element['hdnTaxType'] = 'individual';
        }

        if (empty($element['LineItems']) && $operation == 'create') {
            $element['LineItems'] = [[]];
        }

        return $element;
    }

    /**
     * @param $elementType
     *
     * @return array|mixed
     */
    public function describe($elementType)
    {

    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function create($elementType, $element)
    {
        $element = $this->sanitizeEmptyElement($element);
        $element = $this->sanitizeAssignedUserId($element);
        $element = $this->sanitizeInventoryElement('create', $elementType, $element);

        return $element;
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id)
    {
        return $id;
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function update($elementType, $element)
    {
        $element = $this->sanitizeEmptyElement($element);
        $element = $this->sanitizeAssignedUserId($element);
        $element = $this->sanitizeInventoryElement('update', $elementType, $element);

        return $element;
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function revise($elementType, $element)
    {
        $element = $this->sanitizeEmptyElement($element);
        $element = $this->sanitizeInventoryElement('revise', $elementType, $element);

        return $element;
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $id;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function query($query)
    {
        return $query;
    }

    /**

     */
    public function sync($elementType, $timestamp, $syncType = 'application')
    {
    }

    /**
     * @param $element
     *
     * @return mixed
     */
    public function upload($element)
    {
    }
}
