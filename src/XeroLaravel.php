<?php

namespace Elkbullwinkle\XeroLaravel;

use Elkbullwinkle\XeroLaravel\Exceptions\ApplicationTypeNotAllowedException;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Elkbullwinkle\XeroLaravel\Transport\Transport;
use ReflectionClass;

class XeroLaravel {

    /**
     * Elkbullwinkle\XeroLaravel\XeroLaravel
     *
     * @property-read Transport $transport
     */

    protected $appTypes = [
        'private' => PrivateApplication::class,
    ];

    protected $cats = [
        'accounting' => [
            'name' => 'api.xro',
            'version' => '2.0'
        ]
    ];

    protected $config;

    protected $lastError;

    protected $transport;

    /**
     * @var XeroModel
     */
    protected $model = null;

    /**
     * @var Application
     */
    protected $app;

    public function __construct($config = 'default')
    {
        $this->config = config('xero-laravel.'.$config, []);

        if (!in_array($this->config['type'], array_keys($this->appTypes)))
        {
            throw new ApplicationTypeNotAllowedException();
        }

        $this->app = (new ReflectionClass($this->appTypes[$this->config['type']]))
            ->newInstance(null, $this->config);

        //Ugly need to redo
        $this->transport = &$this->_transport;

        return $this;
    }

    public function processResponse($response)
    {
        if (!$response['status'])
        {
            $this->lastError = [
                'code' => $response['code'],
                'error' => $response['body'],
            ];

            return false;
        }

        return $response['body'][$this->model->getEndpoint()];
    }

    final public function get($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('get', $url, $data));
    }

    final public function post($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('get', $url, $data));
    }

    final public function put($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('get', $url, $data));
    }

    final public function delete($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('get', $url, $data));
    }

    public function setModel(XeroModel &$model)
    {
        $this->model = &$model;

        return $this;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }


    public function convertEndpointToUrl($guid = null)
    {
        $baseUrl = $this->config['base_url'];

        $urlBits = $this->cats[$this->model->getCat()];

        $url = sprintf("%s/%s/%s/%s", $baseUrl, $urlBits['name'], $urlBits['version'], $this->model->getEndpoint());

        if (!is_null($guid) && trim($guid) != '')
        {
            $url .= "/${guid}";
        }

        return $url;

    }

    public function __get($name)
    {
        if ($name == '_transport')
        {
            return $this->app->getTransport();
        }

        if ($name == 'baseUrl')
        {
            return $this->config['base_url'];
        }
    }

}