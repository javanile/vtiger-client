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

class ElementSanitizer
{
    /**
     * @var string
     */
    protected $defaultAssignedUserId;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {

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
     * @param string $elementType moduleName
     * @param string|int|DateTime $timestamp Last known modified time from where you are expecting state changes of records, it should be in unix timestamp.
     * @param string $syncType  user: fetch records restricted to assigned owner of record.
     *
     *                          userandgroup: fetch records restricted to assigned owner of own’s group.
     *
     *                          application: fetch records without restriction on assigned owner.
     *
     * @return mixed
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
