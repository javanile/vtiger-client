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

class VtigerClient extends HttpClient
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $sessionName;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var TypesManager
     */
    protected $typesManager;

    /**
     * @var OperationMapper
     */
    protected $operationMapper;

    /**
     * @var ElementSanitizer
     */
    protected $elementSanitizer;

    /**
     * @var array
     */
    protected $elementValidator;

    /**
     *
     */
    protected $lineItemManager;

    /**
     *
     */
    protected $depthManager;

    /**
     *
     */
    protected $profiler;

    /**
     * Constructor.
     *
     * @param mixed $args
     */
    public function __construct($args)
    {
        if (!is_array($args)) {
            $args = ['endpoint' => $args];
        }

        $this->username = isset($args['username']) && $args['username'] ? $args['username'] : null;
        $this->accessKey = isset($args['accessKey']) && $args['accessKey'] ? $args['accessKey'] : null;

        parent::__construct($args);

        $this->typesManager = new TypesManager($args, $this);
        $this->operationMapper = new OperationMapper($args);
        $this->elementSanitizer = new ElementSanitizer($args);
        $this->elementValidator = new ElementValidator($args, $this->getLogger());
        $this->lineItemManager = new LineItemManager($this);
        $this->depthManager = new DepthManager($this);
        $this->profiler = new Profiler($args);
    }

    /**
     * Perform get challenge operation.
     *
     * @param null|mixed $username
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function getChallenge($username = null)
    {
        $this->profiler->begin(__METHOD__);

        if ($username !== null) {
            $this->username = $username;
            $this->profiler->setTag($this->username, $this->endpoint);
            $this->logger->setTag($this->username, $this->endpoint);
        }

        $json = $this->get([
            'query' => [
                'operation' => $this->operationMapper->get('getchallenge'),
                'username'  => $this->username,
            ],
        ]);

        $this->token = isset($json['result']['token']) ? $json['result']['token'] : null;

        return $this->profiler->end(__METHOD__, $json);
    }

    /**
     * Retrieve challenge token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Perform login action.
     *
     * @param null|mixed $username
     * @param null|mixed $accessKey
     *
     * @return array|mixed
     */
    public function login($username = null, $accessKey = null)
    {
        if ($username !== null) {
            $this->username = $username;
            $this->profiler->setTag($this->username, $this->endpoint);
            $this->logger->setTag($this->username, $this->endpoint);
        }

        if ($accessKey !== null) {
            $this->accessKey = $accessKey;
        }

        if ($this->token === null) {
            $this->getChallenge();
        }

        $json = $this->post([
            'form_params' => [
                'operation' => $this->operationMapper->get('login'),
                'username'  => $this->username,
                'accessKey' => md5($this->token.$this->accessKey),
            ],
        ]);

        $this->sessionName = isset($json['result']['sessionName'])
            ? $json['result']['sessionName'] : null;

        $this->userId = isset($json['result']['userId']) ? $json['result']['userId'] : null;

        if ($this->userId) {
            $this->elementSanitizer->setDefaultAssignedUserId($this->userId);
        }

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
        $response = $this->get([
            'query' => [
                'operation'   => $this->operationMapper->get('listtypes'),
                'sessionName' => $this->sessionName,
            ],
        ]);

        if ($response['success']) {
            $this->typesManager->setTypes($response['result']);
        }

        return $response;
    }

    /**
     * Get list name of types.
     */
    public function getTypes()
    {
        if (!$this->typesManager->hasTypes()) {
            $this->listTypes();
        }

        return $this->typesManager->getTypes();
    }

    /**
     * Get list name of types.
     */
    public function getTypeByElementId($id)
    {
        if (!$this->typesManager->hasTypes()) {
            $this->listTypes();
        }

        return $this->typesManager->getTypeByElementId($id);
    }

    /**
     * Describe an element type structure.
     *
     * @param $elementType
     * @param int $depth
     *
     * @return array|mixed
     */
    public function describe($elementType, $depth = 0)
    {
        $validate = $this->elementValidator->describe($elementType);

        if (!$validate['success']) {
            return $validate;
        }

        if ($depth > 0) {
            return $this->depthManager->describe($elementType, $depth);
        }

        return $this->get([
            'query' => [
                'operation'   => $this->operationMapper->get('describe'),
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
        $element = $this->elementSanitizer->create($elementType, $element);
        $validate = $this->elementValidator->create($elementType, $element);

        if (empty($validate['success'])) {
            return $validate;
        }

        return $this->post([
            'form_params' => [
                'operation'   => $this->operationMapper->get('create'),
                'element'     => json_encode($element),
                'elementType' => $elementType,
                'sessionName' => $this->sessionName,
            ],
        ]);
    }

    /**
     * Retrieve
     *
     * @param mixed $id
     * @param int $depth
     *
     * @return mixed
     */
    public function retrieve($id, $depth = 0)
    {
        if ($depth > 0) {
            return $this->depthManager->retrieve($id, $depth);
        }

        return $this->get([
            'query' => [
                'operation'   => $this->operationMapper->get('retrieve'),
                'id'          => $id,
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
    public function update($elementType, $element)
    {
        $element = $this->elementSanitizer->update($elementType, $element);
        $validate = $this->elementValidator->update($elementType, $element);

        if (empty($validate['success'])) {
            return $validate;
        }

        return $this->post([
            'form_params' => [
                'operation'     => $this->operationMapper->get('update'),
                'element'       => json_encode($element),
                'elementType'   => $elementType,
                'sessionName'   => $this->sessionName,
            ],
        ]);
    }

    /**
     * @param $elementType
     * @param $element
     *
     * @return mixed
     */
    public function revise($elementType, $element)
    {
        /*
        $element = $this->elementSanitizer->update($elementType, $element);
        $validate = $this->elementValidator->update($elementType, $element);

        if (empty($validate['success'])) {
            return $validate;
        }
        */

        return $this->post([
            'form_params' => [
                'operation'     => $this->operationMapper->get('revise'),
                'element'       => json_encode($element),
                'elementType'   => $elementType,
                'sessionName'   => $this->sessionName,
            ],
        ]);
    }

    /**
     * @param $crmid
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->post([
            'form_params' => [
                'operation'     => $this->operationMapper->get('delete'),
                'id'            => $id,
                'sessionName'   => $this->sessionName,
            ],
        ]);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function query($query)
    {
        return $this->get([
            'query' => [
                'operation'   => $this->operationMapper->get('query'),
                'query'       => trim(trim($query), ';').';',
                'sessionName' => $this->sessionName,
            ],
        ]);
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
    public function sync($elementType, $timestamp, $syncType = 'application', $depth = 0)
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

        if ($depth > 0) {
            return $this->depthManager->sync($elementType, $timestamp, $syncType, $depth);
        }

        return $this->get([
            'query' => [
                'operation'     => $this->operationMapper->get('sync'),
                'elementType'   => $elementType,
                'modifiedTime'  => $timestamp,
                'syncType'      => $syncType,
                'sessionName'   => $this->sessionName,
            ],
        ]);
    }

    /**
     * Upload file as Document.
     *
     * @param $element
     *
     * @return mixed
     */
    public function upload($element)
    {
        $file = $element['filename'];

        return $this->post([
            'multipart' => [
                ['name' => 'operation', 'contents' => 'create'],
                ['name' => 'elementType', 'contents' => 'Documents'],
                ['name' => 'element', 'contents' => json_encode($element)],
                ['name' => 'sessionName', 'contents' => $this->sessionName],
                ['name' => 'filename', 'contents' => file_get_contents($file), 'filename' => $file],
            ]
        ]);
    }

    /**
     *
     */
    public function listUsers()
    {
        return $this->query('SELECT * FROM Users;');
    }
}
