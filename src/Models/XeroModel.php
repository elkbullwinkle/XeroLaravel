<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 5:53 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models;


abstract class XeroModel
{
    protected $pageable = false;

    /**
     * @return bool
     */
    public function isPageable()
    {
        return $this->pageable;
    }

}