<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Address extends XeroModel
{
    protected $pageable = false;
    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = '';

    protected $id = '';

    protected $required = [
        'AddressType',
    ];

    protected $attrs = [

        'AddressType' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'AddressLine1' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'AddressLine2' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'AddressLine3' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'AddressLine4' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'City' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'Region' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'PostalCode' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'Country' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'AttentionTo' => [
            'type' => 'string',
            'put',
            'post',
        ],

    ];
}