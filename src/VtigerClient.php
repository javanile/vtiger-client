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

class VtigerClient
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $sessionName;

    /**
     * @var array
     */
    protected $types;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $operationsMap = [
        'getchallenge' => 'getchallenge',
        'login' => 'login',
        'listtypes' => 'listtypes',
        'describe' => 'describe',
        'create' => 'create',
        'retrieve' => 'retrieve',
        'update' => 'update',
        'delete' => 'delete',
        'query' => 'query',
        'sync' => 'sync',
    ];

    /**
     * Constructor.
     *
     * @param mixed $args
     */
    public function __construct($args)
    {
        if (!is_array($args)) {
            $args = ['endpoint' => $args];
        } else {
            if (array_key_exists('operationsMap', $args) && is_array($args['operationsMap'])) {
                foreach (array_keys($this->operationsMap) as $operation) {
                    if (array_key_exists($operation, $args['operationsMap'])) {
                        $this->operationsMap[$operation] = $args['operationsMap'];
                    }
                }
            }
        }

        $this->endpoint = $args['endpoint'].'/webservice.php';
        $this->username = isset($args['username']) ? $args['username'] : null;
        $this->accessKey = isset($args['accessKey']) ? $args['accessKey'] : null;

        $this->client = new Client();
    }

    /**
     * Perform get challenge operation.
     *
     * @param null|mixed $username
     */
    public function getChallenge($username = null)
    {
        if ($username !== null) {
            $this->username = $username;
        }

        $json = $this->get([
            'query' => [
                'operation' => $this->operationsMap['getchallenge'],
                'username'  => $this->username,
            ],
        ]);

        $this->token = isset($json['result']['token']) ? $json['result']['token'] : null;

        return $json;
    }

    /**
     * Retrieve challenge token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Performi login action.
     *
     * @param null|mixed $username
     * @param null|mixed $accessKey
     */
    public function login($username = null, $accessKey = null)
    {
        if ($username !== null) {
            $this->username = $username;
        }

        if ($accessKey !== null) {
            $this->accessKey = $accessKey;
        }

        if ($this->token === null) {
            $this->getChallenge();
        }

        $json = $this->post([
            'form_params' => [
                'operation' => $this->operationsMap['login'],
                'username'  => $this->username,
                'accessKey' => md5($this->token.$this->accessKey),
            ],
        ]);

        $this->sessionName = isset($json['result']['sessionName'])
            ? $json['result']['sessionName'] : null;

        return $json;
    }

    /**
     * Retrieve the login session name.
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     *
     */
    public function listTypes()
    {
        $json = $this->get([
            'query' => [
                'operation'   => $this->operationsMap['listtypes'],
                'sessionName' => $this->sessionName,
            ],
        ]);

        $this->types = isset($json['result']['types']) ? $json['result']['types'] : null;

        return $json;
    }

    /**
     * Get list name of types.
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param $elementType
     */
    public function describe($elementType)
    {
        $json = $this->get([
            'query' => [
                'operation'   => $this->operationsMap['describe'],
                'elementType' => $elementType,
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
    public function create($elementType, $element)
    {
        $json = $this->post([
            'form_params' => [
                'operation'   => $this->operationsMap['create'],
                'element'     => json_encode($element),
                'elementType' => $elementType,
                'sessionName' => $this->sessionName,
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

    /**
     *
     */
    public function listUsers()
    {
        $json = $this->query('SELECT * FROM Users;');

        return $json;
    }

    /**
     * @param $flag
     * @param mixed $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param $user_password
     * @param $user_name
     * @param string $crypt_type
     *
     * @return string
     */
    protected static function encryptPassword($user_password, $user_name, $crypt_type = '')
    {
        $salt = substr($user_name, 0, 2);

        if ($crypt_type == '') {
            $crypt_type = 'PHP5.3MD5';
        }

        if ($crypt_type == 'MD5') {
            $salt = '$1$'.$salt.'$';
        } elseif ($crypt_type == 'BLOWFISH') {
            $salt = '$2$'.$salt.'$';
        } elseif ($crypt_type == 'PHP5.3MD5') {
            $salt = '$1$'.str_pad($salt, 9, '0');
        }

        $encrypted_password = crypt($user_password, $salt);

        return $encrypted_password;
    }

    /**
     * @param $response
     */
    protected function decodeResponse($response)
    {
        $body = $response->getBody()->getContents();
        $json = json_decode($body, true);

        if (!$body && !$json) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'EMPTY_RESPONSE',
                    'message' => 'Web service send an empty body',
                ],
            ];
        }

        if ($body && !$json) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'JSON_PARSE_ERROR',
                    'message' => $body,
                ],
            ];
        }

        return $json;
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    protected function get($request)
    {
        try {
            $response = $this->client->request('GET', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'GUZZLE_ERROR',
                    'message' => $error->getMessage(),
                ],
            ];
        }

        return $this->decodeResponse($response);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    protected function post($request)
    {
        try {
            $response = $this->client->request('POST', $this->endpoint, $request);
        } catch (GuzzleException $error) {
            return [
                'success' => false,
                'error'   => [
                    'code'    => 'GUZZLE_ERROR',
                    'message' => $error->getMessage(),
                ],
            ];
        }

        return $this->decodeResponse($response);
    }
}
