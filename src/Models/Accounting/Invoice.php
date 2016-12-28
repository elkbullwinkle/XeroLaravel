<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Invoice extends XeroModel
{
    protected $pageable = false;

    protected $required = [
        'Type',
        'Contact',
        'LineItems',
    ];
}