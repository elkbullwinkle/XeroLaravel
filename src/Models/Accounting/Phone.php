<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Phone extends XeroModel
{
    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = '';

    protected $id = '';

    protected $required = [
        'PhoneType',
    ];

    protected $attrs = [

        'PhoneType' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'PhoneNumber' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'PhoneAreaCode' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'PhoneCountryCode' => [
            'type' => 'string',
            'post',
            'put'
        ],

    ];
}