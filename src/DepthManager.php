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
     * @param $fields
     * @param int $depth
     *
     * @return mixed
     */
    private function getFieldsByDepth($fields, $depth = 0)
    {
        if ($depth == 0) {
            return $fields;
        }

        $relatedFields = [];
        foreach ($fields as $index => $field) {
            if ($field['type']['name'] == 'owner') {
                $field['type']['refersTo'] = ['Users'];
                $fields[$index]['type']['refersTo'] = ['Users'];
            }
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
                    if (empty($relatedFields[$relatedField['name']])) {
                        $relatedFields[$relatedField['name']] = $relatedField;
                    }
                }
            }
        }

        foreach ($fields as $field) {
            if (isset($relatedFields[$field['name']])) {
                unset($relatedFields[$field['name']]);
            }
        }

        return $this->getFieldsByDepth(array_merge($fields, array_values($relatedFields)), $depth - 1);
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
     * @param mixed $id
     * @param $maxDepth
     *
     * @return mixed
     */
    public function retrieve($id, $maxDepth, $type = null)
    {
        $retrieve = $this->client->retrieve($id);

        if (empty($type)) {
            $type = $this->client->getTypeByElementId($id);
            if (empty($type)) {
                return $retrieve;
            }
        }

        $describe = $this->client->describe($type, $maxDepth);
        if (empty($describe['result']['fields'])) {
            return $retrieve;
        }

        $relatedElements = [];
        foreach ($describe['result']['fields'] as $field) {
            if (empty($field['type']['refersTo']) || empty($field['depth'])) {
                continue;
            }
            if (empty($retrieve['result'][$field['name']])) {
                continue;
            }
            $value = $retrieve['result'][$field['name']];
            if (empty($relatedElements[$value])) {
                $relatedElements[$value] = $this->client->retrieve($value);
            }
            if (empty($relatedElements[$value]['result'])) {
                continue;
            }
            foreach ($relatedElements[$value]['result'] as $relatedField => $relatedValue) {
                $retrieve['result'][$field['name'].'__'.$relatedField] = $relatedValue;
            }
        }

        return $retrieve;
    }

    /**
     * Increace number of fields for sync operation.
     *
     * @param $elementType
     * @param $timestamp
     * @param $syncType
     * @param $maxDepth
     *
     * @return mixed
     */
    public function sync($elementType, $timestamp, $syncType, $maxDepth)
    {
        $sync = $this->client->sync($elementType, $timestamp, $syncType);
        if (empty($sync['result']['updated'])) {
            return $sync;
        }

        $describe = $this->client->describe($elementType, $maxDepth);
        if (empty($describe['result']['fields'])) {
            return $sync;
        }

        $relatedElements = [];
        foreach ($sync['result']['updated'] as $index => $element) {
            foreach ($describe['result']['fields'] as $field) {
                if (empty($field['type']['refersTo']) || empty($field['depth'])) {
                    continue;
                }
                if (empty($element[$field['name']])) {
                    continue;
                }
                $value = $element[$field['name']];
                if (empty($relatedElements[$value])) {
                    $relatedElements[$value] = $this->client->retrieve($value);
                }
                if (empty($relatedElements[$value]['result'])) {
                    continue;
                }
                foreach ($relatedElements[$value]['result'] as $relatedField => $relatedValue) {
                    $sync['result']['updated'][$index][$field['name'].'__'.$relatedField] = $relatedValue;
                }
            }
        }

        return $sync;
    }
}
