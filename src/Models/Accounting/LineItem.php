<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class LineItem extends XeroModel
{
    protected $pageable = false;

    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = '';

    protected $id = 'LineItemID';

    protected $required = [
        'Description',
    ];

    protected $collections = [
        'Tracking',
    ];

    protected $attrs = [
        'LineItemID' => 'guid',

        'Description' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'Quantity' => [
            'type' => 'float',
            'put',
            'post'
        ],

        'UnitAmount' => [
            'type' => 'float',
            'put',
            'post'
        ],

        'ItemCode' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'AccountCode' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'TaxType' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'TaxAmount' => [
            'type' => 'float',
            'put',
            'post'
        ],

        'LineAmount' => [
            'type' => 'float',
            'put',
            'post'
        ],

        'Tracking' => [
            'type' => ElementTracking::class,
            'put',
            'post'
        ],

        'DiscountRate' => [
            'type' => 'float',
            'put',
            'post'
        ],

    ];
}