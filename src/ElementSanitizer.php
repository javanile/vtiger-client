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
        $validate = $this->elementValidator->describe($elementType);

        if (!$validate['success']) {
            return $validate;
        }

        return $this->get([
            'query' => [
                'operation'   => $this->operationsMap['describe'],
                'elementType' => $elementType,
                'sessionName' => $this->sessionName,
            ],
        ]);
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function create($elementType, $element)
    {
        $sanitizedElement = $this->elementSanitizer->create($elementType, $element);

        $validate = $this->elementValidator->create($elementType, $sanitizedElement);

        if (!$validate['success']) {
            return $validate;
        }

        return $this->post([
            'form_params' => [
                'operation'   => $this->operationMappper->get('create'),
                'element'     => json_encode($sanitizedElement),
                'elementType' => $elementType,
                'sessionName' => $this->sessionName,
            ],
        ]);
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id)
    {
        $json = $this->get([
            'query' => [
                'operation'   => $this->operationsMap['retrieve'],
                'id'          => $id,
                'sessionName' => $this->sessionName,
            ],
        ]);

        return $json;
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function update($elementType, $element)
    {
        $json = $this->post([
            'form_params' => [
                'operation'     => $this->operationsMap['update'],
                'element'       => json_encode($element),
                'elementType'   => $elementType,
                'sessionName'   => $this->sessionName,
            ],
        ]);

        return $json;
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $json = $this->post([
            'form_params' => [
                'operation'     => $this->operationsMap['delete'],
                'id'            => $id,
                'sessionName'   => $this->sessionName,
            ],
        ]);

        return $json;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function query($query)
    {
        $query = trim(trim($query), ';').';';

        $json = $this->get([
            'query' => [
                'operation'   => $this->operationsMap['query'],
                'query'       => $query,
                'sessionName' => $this->sessionName,
            ],
        ]);

        return $json;
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
        if (!in_array($syncType, ['user', 'userandgroup', 'application'])) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'WRONG_SYNCTYPE',
                    'message' => '$syncType must be on of "user", "userandgroup" or "application"',
                ],
            ];
        }

        if ($timestamp instanceof \DateTime) {
            $timestamp = $timestamp->format('U');
        }

        if (!is_numeric($timestamp)) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'WRONG_TIMESTAMP',
                    'message' => '$timestamp must be a valid unix time or a instance of DateTime',
                ],
            ];
        }


        $json = $this->get([
            'query' => [
                'operation'     => $this->operationsMap['sync'],
                'elementType'   => $elementType,
                'modifiedTime'  => $timestamp,
                'syncType'      => $syncType,
                'sessionName'   => $this->sessionName,
            ],
        ]);

        return $json;
    }

    /**
     * @param $element
     *
     * @return mixed
     */
    public function upload($element)
    {
        $file = $element['filename'];

        $json = $this->post([
            'multipart' => [
                ['name' => 'operation', 'contents' => 'create'],
                ['name' => 'elementType', 'contents' => 'Documents'],
                ['name' => 'element', 'contents' => json_encode($element)],
                ['name' => 'sessionName', 'contents' => $this->sessionName],
                ['name' => 'filename', 'contents' => file_get_contents($file), 'filename' => $file],
            ]
        ]);

        return $json;
    }
}
