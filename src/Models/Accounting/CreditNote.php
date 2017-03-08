<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class CreditNote extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'CreditNotes';

    protected $id = 'CreditNoteID';

    protected $required = [
        'Date',
        'Amount',
        'InvoiceID',
    ];

    protected $collections = [
        'LineItems',
    ];

    protected $attrs = [

        //GUID resource ID

        'CreditNoteID' => [
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

        'FullyPaidOnDate' => 'net-date',

        //Optional for PUT/POST requests

        'CreditNoteNumber' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'Reference' => [
            'type' => 'string',
            'post',
            'put',
            'if' => [
                'attribute' => 'Type',
                'value' => 'ACCRECCREDIT'
            ],
        ],

        'SentToContact' => [
            'type' => 'boolean',
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

        'BrandingThemeID' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'HasAttachments' => 'boolean',

    ];
}