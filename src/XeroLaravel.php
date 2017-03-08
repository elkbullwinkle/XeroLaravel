<?php

namespace Elkbullwinkle\XeroLaravel;

use Elkbullwinkle\XeroLaravel\Exceptions\ApplicationTypeNotAllowedException;
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

    protected $config;

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

        return $this;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    public function __get($name)
    {
        if ($name == 'transport')
        {
            return $this->app->getTransport();
        }

        if ($name == 'baseUrl')
        {
            return $this->config['base_url'];
        }
    }

}