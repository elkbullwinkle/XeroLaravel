<?php

namespace Elkbullwinkle\XeroLaravel;

use Elkbullwinkle\XeroLaravel\Exceptions\SignatureTypeNotAllowedException;
use Elkbullwinkle\XeroLaravel\Transport\Guzzle;
use Elkbullwinkle\XeroLaravel\Transport\Transport;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use ReflectionClass;

abstract class Application {

    /**
     * @var Transport
     */
    protected $transport;

    protected $signatureType = '';

    protected $type = '';

    const defaultTransport = Guzzle::class;

    public function __construct(Transport $transport = null, $config)
    {
        if (is_null($transport))
        {
            $transport = (new ReflectionClass(self::defaultTransport))->newInstance();
        }

        $transport->setSignatureType($this->signatureType);

        switch (strtolower($this->signatureType))
        {
            default:
                throw new SignatureTypeNotAllowedException(sprintf("\"%s\" is not a valid signature type", $this->signatureType));

            case 'two_legged':
                $transport->addHeader('User-Agent', $config['app_name'])
                    ->addHeader('Accept', 'application/json')
                    ->setBaseUrl($config['base_url'])
                    ->setConsumerKey($config['consumer_key'])
                    ->setToken($config['consumer_key'])
                    ->setPrivateKeyFile($config['private_key'])
                    ->setPrivateKeyPassphrase('')
                    ->setSignatureMethod(Oauth1::SIGNATURE_METHOD_RSA)
                    ->init();
        }

        $this->transport = $transport;

    }

    /**
     * @return Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param string $signatureType
     * @return Application
     */
    public function setSignatureType($signatureType)
    {
        $this->signatureType = $signatureType;

        //$this->transport->setSignatureType($signatureType);

        return $this;
    }
}