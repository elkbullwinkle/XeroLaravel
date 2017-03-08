<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Overpayment extends XeroModel
{
    protected $pageable = true;

    protected $cat = 'accounting';

    protected $endpoint = 'Overpayments';

    protected $id = 'OverpaymentID';

    protected $required = [
    ];

    protected $collections = [
        'LineItems',
    ];

    protected $attrs = [

        //GUID resource ID

        'OverpaymentID' => [
            'type' => 'guid',
        ],

        //Required to POST a draft Invoice

        'Type' => [
            'type' => 'string',
            'post',
            'put',
            'required',
        ],

        'Contact' => [
            'type' => Contact::class,
            'post',
            'put',
            'required',
        ],

        //Recommended for POST/PUT requests

        //'DateString' => 'date', //We don't have to use it since .Net date is parsed anyway

        'Date' => [
            'type' => 'net-date',
            'post',
            'put',
        ],

        'Status' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'LineAmountTypes' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'LineItems' => [
            'type' => LineItem::class,
            'post',
            'put',
            'required',
        ],

        'SubTotal' => 'float',

        'TotalTax' => 'float',

        'Total' => 'float',

        'CurrencyCode' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'CurrencyRate' => [
            'type' => 'float',
            'post',
            'put'
        ],

        'RemainingCredit' => 'float',

        /*'Allocations' => [
            'type' => '',
        ],*/

        'Payments' => [
            'type' => Payment::class,
        ],
        
        'HasAttachments' => 'boolean',

    ];
}