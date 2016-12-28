<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 4:47 PM
 */

namespace Elkbullwinkle\XeroLaravel;


use Elkbullwinkle\XeroLaravel\Transport\Transport;

class PublicApplication extends Application
{

    /**
     * PublicApplication constructor.
     * @param Transport|null $transport
     */
    public function __construct(Transport $transport = null)
    {
        parent::__construct($transport);

        $this->setSignatureType('two_legged');



    }



}