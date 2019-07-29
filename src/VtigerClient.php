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

class VtigerClient
{
    /**
     *
     */
    protected $token;

    /**
     *
     */
    protected $sessionName;

    /**
     *
     */
    protected $types;

    /**
     *
     */
    protected $client;

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

        $this->endpoint = $args['endpoint'].'/webservice.php';
        $this->username = isset($args['username']) ? $args['username'] : null;
        $this->accessKey = isset($args['accessKey']) ? $args['accessKey'] : null;

        $this->client = new \GuzzleHttp\Client();
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
                'operation' => 'getchallenge',
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
                'operation' => 'login',
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
                'operation'   => 'listtypes',
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
                'operation'   => 'describe',
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
                'operation'   => 'create',
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
                'operation'   => 'retrieve',
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
                'operation'		 => 'update',
                'element'		   => json_encode($element),
                'elementType'	=> $elementType,
                'sessionName'	=> $this->sessionName,
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
                'operation'		 => 'delete',
                'id'			       => $id,
                'sessionName'	=> $this->sessionName,
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
                'operation'   => 'query',
                'query'       => $query,
                'sessionName' => $this->sessionName,
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
        /*_*/
        $file = $element['filename'];

        $json = $this->post([
            'multipart' => [
                [ 'name' => 'operation', 'contents' => 'create'],
                [ 'name' => 'elementType', 'contents' => 'Documents'],
                [ 'name' => 'element', 'contents' => json_encode($element)],
                [ 'name' => 'sessionName', 'contents' => $this->sessionName],
                [ 'name' => 'filename', 'contents' => file_get_contents($file), 'filename' => $file],
            ]
        ]);

        /*/
        $url = "{$this->endpoint}";

        $filedata = $element['filename'];
        $filename = basename($filedata);
        $filesize = filesize($filedata);

        $headers = ['Content-Type:multipart/form-data'];
        $postfields = [
            'operation'   => 'create',
            'elementType' => 'Documents',
            'element'     => json_encode($element),
            'sessionName' => $this->sessionName,
            'filename'    => "@$filedata",
        ];

        $ch = curl_init();
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => 1,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $postfields,
            CURLOPT_INFILESIZE     => $filesize,
            CURLOPT_RETURNTRANSFER => true,
        ];

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        var_Dump($result);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            var_dump($info);
            if ($info['http_code'] == 200) {
                $errmsg = 'File uploaded successfully';
            }
        } else {
            $errmsg = curl_error($ch);
        }

        curl_close($ch);

        $json = json_decode($result);
        /*_*/
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
            var_dump($body);
            die();
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
        $response = $this->client->request('GET', $this->endpoint, $request);

        return $this->decodeResponse($response);
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    protected function post($request)
    {
        $response = $this->client->request('POST', $this->endpoint, $request);

        return $this->decodeResponse($response);
    }
}
