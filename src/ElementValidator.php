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

class ElementValidator
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
        if (empty($element)) {
            //return Factory::createError('EMPTY_ELEMENT', 'Empty element in create');
        }

        return Factory::createSuccess();
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id)
    {

    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function update($elementType, $element)
    {

    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id)
    {

    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function query($query)
    {

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
