<?php
/**
 * File description.
 *
 * PHP version 5
 *
 * @category -
 *
 * @author    -
 * @copyright -
 * @license   -
 */

namespace Javanile\VtigerClient;

class VtigerClient
{
    /**
     *
     */
    private $challengeToken;

    /**
     *
     */
    private $sessionName;

    /**
     * Constructor.
     */
    public function __construct($args)
    {
        $this->username = $args['username'];
        $this->endpoint = $args['endpoint'].'/webservice.php';
        $this->accessKey = $args['accessKey'];

        $this->client = new \GuzzleHttp\Client();
    }

    /**
     *
     */
    public function doGetChallenge()
    {
        $response = $this->client->request('GET', $this->endpoint, [
            'query' => [
                'operation' => 'getchallenge',
                'username'  => $this->username,
            ]
        ]);

        $body = $response->getBody();

        $json = json_decode($body, true);

        $this->challengeToken = $json['result']['token'];

        return $body;
    }

    /**
     *
     */
    public function getChallengeToken()
    {
        return $this->challengeToken;
    }

    /**
     *
     */
    public function doLogin()
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'form_params' => [
                'operation' => 'login',
                'username'  => $this->username,
                'accessKey' => md5($this->challengeToken.$this->accessKey)
            ]
        ]);

        $body = $response->getBody();
        $json = json_decode($body, true);

        if (isset($json['result'])) {
            $this->sessionName = $json['result']['sessionName'];
        }

        return $body;
    }
}
