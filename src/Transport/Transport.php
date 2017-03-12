<?php

namespace Elkbullwinkle\XeroLaravel\Transport;

abstract class Transport {

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = [
    ];


    protected $headers = [];

    protected $lastError = [];

    /**
     *
     */
    const allowedSignatureType = [
        'two_legged',
        'three_legged',
    ];


    protected $baseUrl = '';

    /**
     * @param $method
     * @param $url
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    abstract function request($method, $url, $data = [], $headers = []);

    final public function get($url, $data = [])
    {
        return $this->request('get', $url, $data = []);
    }

    final public function post($url, $data = [])
    {
        return $this->request('post', $url, $data = []);
    }

    final public function put($url, $data = [])
    {
        return $this->request('put', $url, $data = []);
    }

    final public function delete($url, $data = [])
    {
        return $this->request('delete', $url, $data = []);
    }

    /**
     * @param array $config
     * @return $this
     */
    final public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * @return array
     */
    final public function getConfig()
    {
        return $this->config;
    }

    final public function setConsumerKey($key)
    {
        $this->config['consumer_key'] = $key;

        return $this;
    }

    /**
     * @param $secret
     * @return $this
     */
    final public function setConsumerSecret($secret)
    {
        $this->config['consumer_secret'] = $secret;
        $this->config['token_secret'] = $secret;

        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    final public function setToken($token)
    {
        $this->config['token'] = $token;

        return $this;
    }

    /**
     * @param $secret
     * @return $this
     */
    final public function setTokenSecret($secret)
    {
        $this->config['token_secret'] = $secret;

        return $this;
    }

    /**
     * @param $file
     * @return $this
     */
    final public function setPrivateKeyFile($file)
    {
        $this->config['private_key_file'] = $file;

        return $this;
    }

    /**
     * @param $passphrase
     * @return $this
     */
    final public function setPrivateKeyPassphrase($passphrase)
    {
        $this->config['private_key_passphrase'] = $passphrase;

        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    final public function setSignatureMethod($method)
    {
        $this->config['signature_method'] = $method;

        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    final public function setSignatureType($type)
    {
        $this->config['signature_type'] = $type;

        return $this;
    }

    /**
     * @param string $baseUrl
     * @return Transport
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @param array $headers
     * @return Transport
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @param $header
     * @param $value
     * @return $this
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * @param $url
     * @return string
     */
    public function url($url)
    {
        return trim($this->baseUrl.$url);
    }

}