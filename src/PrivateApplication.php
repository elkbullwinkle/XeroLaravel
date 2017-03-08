<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 4:47 PM
 */

namespace Elkbullwinkle\XeroLaravel;


use Elkbullwinkle\XeroLaravel\Transport\Transport;

class PrivateApplication extends Application
{

    /**
     * PublicApplication constructor.
     * @param Transport|null $transport
     * @param array $config
     */
    public function __construct(Transport $transport = null, $config)
    {

        $this->type = 'private';

        $this->setSignatureType('two_legged');

        parent::__construct($transport, $config);

    }



}