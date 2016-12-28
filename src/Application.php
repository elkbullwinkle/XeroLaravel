<?php

namespace Elkbullwinkle\XeroLaravel;

use Elkbullwinkle\XeroLaravel\Transport\Guzzle;
use Elkbullwinkle\XeroLaravel\Transport\Transport;
use ReflectionClass;

abstract class Application {

    /**
     * @var Transport
     */
    protected $transport;

    protected $signatureType = '';


    const defaultTransport = Guzzle::class;

    public function __construct(Transport $transport = null)
    {
        if (is_null($transport))
        {
            $transport = new ReflectionClass(self::defaultTransport);
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
     * @param string $signatureType
     * @return Application
     */
    public function setSignatureType($signatureType)
    {
        $this->signatureType = $signatureType;

        $this->transport->setSignatureType($signatureType);

        return $this;
    }
}