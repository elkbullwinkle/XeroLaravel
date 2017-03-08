<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Item extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'Items';

    protected $id = 'ItemID';

    protected $required = [
        'Code',
    ];

    protected $attrs = [

        'ItemID' => 'guid',

        'Code' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'InventoryAssetAccountCode' => [
            'type' => 'int',
            'post',
            'put'
        ],

        'Name' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'IsSold' => [
            'type' => 'boolean',
            'post',
            'put'
        ],

        'IsPurchased' => [
            'type' => 'boolean',
            'post',
            'put'
        ],

        'Description' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'PurchaseDescription' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'PurchaseDetails' => [
            'type' => 'array',
            'post',
            'put'
        ],

        'SalesDetails' => [
            'type' => 'array',
            'post',
            'put'
        ],

        //Read only retrieved on GET requests

        'IsTrackedAsInventory' => 'boolean',

        'TotalCostPool' => 'float',

        'QuantityOnHand' => 'float',

    ];
}