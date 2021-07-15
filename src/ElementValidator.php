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

use Javanile\VtigerClient\System\Response;

class ElementValidator
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * HttpClient constructor.
     *
     * @param $args
     */
    public function __construct($args, $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $elementType
     *
     * @return array|mixed
     */
    public function describe($elementType)
    {
        return Response::success();
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
            return Response::error('EMPTY_ELEMENT', 'Empty element in create');
        }

        if ($elementType == 'LineItem' && empty($element['parent_id'])) {
            return Response::error('MISSING_PARENT_ID', 'Missing parent_id on LineItem element');
        }

        return Response::success();
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
        if (empty($element)) {
            return Response::error('EMPTY_ELEMENT', 'Empty element on update');
        }

        return Response::success();
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
