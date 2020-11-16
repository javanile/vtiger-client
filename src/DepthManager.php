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

class DepthManager
{
    /**
     *
     */
    protected $client;

    /**
     * HttpClient constructor.
     *
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }


    /**
     * Describe element type using depth.
     *
     * @param $elementType
     * @param $maxDepth
     *
     * @return array|mixed
     */
    public function describe($elementType, $maxDepth)
    {
        $response = $this->client->describe($elementType);

        if (empty($response['result']['fields'])) {
            return $response;
        }

        $response['result']['fields'] = $this->getFieldsByDepth($response['result']['fields'], $maxDepth);

        return $response;
    }

    /**
     *
     */
    private function getFieldsByDepth($fields, $depth = 0)
    {
        if ($depth == 0) {
            return $fields;
        }

        $relatedFields = [];
        foreach ($fields as $index => $field) {
            if (empty($field['type']['refersTo']) || isset($field['depth'])) {
                continue;
            }
            $fields[$index]['depth'] = $depth;
            foreach ($field['type']['refersTo'] as $relatedElementType) {
                $response = $this->client->describe($relatedElementType);
                if (empty($response['result']['fields'])) {
                    continue;
                }
                foreach ($response['result']['fields'] as $relatedField) {
                    $relatedField['name'] = $field['name'].'__'.$relatedField['name'];
                    $relatedFields[] = $relatedField;
                }
            }
        }

        return $this->getFieldsByDepth(array_merge($fields, $relatedFields), $depth - 1);
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function create($elementType, $element)
    {
        return $element;
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id, $maxDepth)
    {
        $response = $this->client->retrieve($id);

        for ($depth = 0; $depth < $maxDepth; $depth++) {

        }

        return $response;
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
        $element = $this->sanitizeInventoryElement($elementType, $element);

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