<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 4:45 PM
 */

namespace Elkbullwinkle\XeroLaravel\Transport;

use Elkbullwinkle\XeroLaravel\Exceptions\SignatureTypeNotAllowedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class Guzzle extends Transport
{

    protected $oauth = null;

    /**
     * @var Client
     */
    protected $client = null;

    protected $stack = null;

    public function __construct()
    {

    }

    public function init($signature_type = null)
    {
        return $this->sign($signature_type)
            ->initStack()
            ->initClient();
    }

    protected function initClient()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'handler' => $this->stack,
            'auth' => 'oauth',
        ]);

        return $this;
    }

    protected function initStack()
    {
        $this->stack = HandlerStack::create();

        $this->stack->push($this->oauth);

        return $this;
    }


    /**
     * @param $method
     * @param $url
     * @param array $data
     * @return mixed
     */
    function request($method, $url, $data = [])
    {

        try {
            return $this->client->request(strtoupper($method), $url, ['headers' => $this->headers]);
        } catch (RequestException $e)
        {
            //var_dump($e->getRequest());
            dd($e);

            if ($e->hasResponse())
            {
                //var_dump($e->getResponse());
            }
        }



    }


    public function sign($type = null)
    {
        if (is_null($type))
        {
            $type = $this->config['signature_type'];
        }

        if (!in_array($type, self::allowedSignatureType))
        {
            throw new SignatureTypeNotAllowedException();
        }

        return call_user_func([$this, camel_case('sign_'.$this->config['signature_type'])]);

    }

    protected function signTwoLegged()
    {
        $this->oauth = new Oauth1($this->config);

        return $this;
    }

    protected function signThreeLegged()
    {
        $this->oauth = new Oauth1([
            'consumer_key' => $this->config['consumer_key'],
            'consumer_secret' => $this->config['consumer_secret'],
            'token' => $this->config['token'],
            'token_secret' => $this->config['token_secret'],
        ]);

        return $this;
    }
}