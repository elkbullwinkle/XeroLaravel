<?php

namespace Elkbullwinkle\XeroLaravel;

use Elkbullwinkle\XeroLaravel\Exceptions\ApplicationTypeNotAllowedException;
use Elkbullwinkle\XeroLaravel\Exceptions\XeroLaravelConfigurationException;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Elkbullwinkle\XeroLaravel\Transport\Transport;
use ReflectionClass;

class XeroLaravel {

    /**
     * Supported application types
     *
     * @var array
     */
    protected $appTypes = [
        'private' => PrivateApplication::class,
    ];

    /**
     * Supported API categories and their respective urls and versions
     *
     * @var array
     */
    protected $cats = [
        'accounting' => [
            'name' => 'api.xro',
            'version' => '2.0'
        ]
    ];

    /**
     * Api codes to reattempt the request
     *
     * @var array
     */
    protected $reattemptCodes = [
        401, 500, 503, null
    ];

    /**
     * Api request errors
     *
     * @var array
     */
    protected $requestErrors = [];

    /**
     * Configuration entry for the connection
     *
     * @var string
     */
    protected $config;

    /**
     * Configuration entry name
     *
     * @var string
     */
    protected $configName = '';

    /**
     * Last request error
     *
     * @var array
     */
    protected $lastError;

    /**
     * Transport class instance
     *
     * @var mixed
     */
    protected $transport;

    /**
     * Max number of attempts in case of failed request
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * @var XeroModel
     */
    protected $model = null;

    /**
     * @var Application
     */
    protected $app;

    /**
     * XeroLaravel constructor.
     * @param string $config
     * @throws ApplicationTypeNotAllowedException
     * @throws XeroLaravelConfigurationException
     */
    public function __construct($config = 'default')
    {
        $this->configName = $config;

        $this->config = config('xero-laravel.'.$config, []);

        if (empty($this->config))
        {
            throw new XeroLaravelConfigurationException("Configuration entry \"${config}\" is not defined. Please check your config file");
        }

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

    /**
     * Initialize XeroLaravel core
     *
     * @param string $config configuration matching config file
     * @return XeroLaravel
     */
    public static function init($config = 'default')
    {
        return new static($config);
    }

    /**
     * Process response and log the error
     *
     * @param $response
     * @return array|false
     */
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

    /**
     * Fire get request
     *
     * @param string $guid Guid or Number of the model
     * @param array $data Form data attributes
     * @param array $headers Extra headers
     * @return array|false
     */
    final public function get($guid = null, $data = [], $headers = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        //Implementing attempts business
        $attemptsLeft = $this->maxAttempts;

        while($attemptsLeft > 0)
        {
            $response = $this->transport->request('get', $url, $data, $headers);

            if (!$response['status'])
            {
                $this->requestErrors[] = [
                    'code' => $response['code'],
                    'headers' => $response['headers'],
                    'body' => $response['body'],
                ];

                if (in_array($response['code'], $this->reattemptCodes))
                {
                    $attemptsLeft -= 1;
                    continue;
                }
            }

            break;

        }

        return $this->processResponse($response);
    }

    /**
     * Fire post request
     *
     * @param string $guid Guid or Number of the model
     * @param array $data Form data attributes
     * @return array|false
     */
    final public function post($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('post', $url, $data));
    }

    /**
     * Fire put request
     *
     * @param string $guid Guid or Number of the model
     * @param array $data Form data attributes
     * @return array|false
     */
    final public function put($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('put', $url, $data));
    }

    /**
     * Fire delete request
     *
     * @param string $guid Guid or Number of the model
     * @param array $data Form data attributes
     * @return array|false
     */
    final public function delete($guid = null, $data = [])
    {
        $url = $this->convertEndpointToUrl($guid);

        return $this->processResponse($this->transport->request('delete', $url, $data));
    }

    /**
     * Return the instance of associated model
     *
     * @return XeroModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model for the current connection
     *
     * @param XeroModel $model
     * @return $this
     */
    public function setModel(XeroModel &$model)
    {
        $this->model = &$model;

        return $this;
    }

    /**
     * Return application instance
     *
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }


    /**
     * Convert attached model endpoint to URL
     *
     * @param string $guid
     * @return string
     */
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

    /**
     * Magic get
     *
     * @param $name
     * @return mixed
     */
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