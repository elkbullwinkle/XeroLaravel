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

    protected $cat = 'accounting';

    protected $endpoint = 'Invoices';

    protected $id = 'InvoiceID';

    protected $required = [
        'Type',
        'Contact',
        'LineItems',
    ];

    protected $attrs = [

        //GUID resource ID

        'InvoiceID' => [
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

        'LineItems' => [
            'type' => LineItem::class,
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

        //'DueDateString' => 'date', //We don't have to use it since .Net date is parsed anyway

        'DueDate' => [
            'type' => 'net-date',
            'post',
            'put',
        ],


        'LineAmountTypes' => [
            'type' => 'string',
            'post',
            'put',
        ],

        //Optional for PUT/POST requests

        'InvoiceNumber' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'Reference' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'BrandingThemeID' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'Url' => [
            'type' => 'string',
            'post',
            'put',
        ],

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

        'Status' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'SentToContact' => [
            'type' => 'boolean',
            'post',
            'put',
        ],

        'ExpectedPaymentDate' => [
            'type' => 'boolean',
            'post',
            'put',
        ],

        'PlannedPaymentDate' => [
            'type' => 'boolean',
            'post',
            'put',
        ],

        //Items returned on GET requests

        'SubTotal' => 'float',

        'TotalTax' => 'float',

        'Total' => 'float',

        'TotalDiscount' => 'float',

        'IsDiscounted' => 'boolean', //Undocumented

        'HasAttachments' => 'boolean',

        'Payments' => Payment::class,

        'Prepayments' => Prepayment::class, //Prepayments

        'Overpayments' => Overpayment::class, //Overpayments

        'AmountDue' => [
            'type' => 'float',
        ],

        'AmountPaid' => [
            'type' => 'float',
        ],

        'FullyPaidOnDate' => 'net-date',

        'AmountCredited' => 'float',

        'CreditNotes' => CreditNote::class, //Credit notes

    ];

    protected $collections = [
        'Payments',
        'Prepayments',
        'Overpayments',
        'CreditNotes',
        'LineItems',
    ];
}