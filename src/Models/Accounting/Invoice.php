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
    /**
     * Is collection pageable
     *
     * @var bool
     */
    protected $pageable = true;

    /**
     * Model category, probably can be figured out using namespaces
     *
     * @var string
     */
    protected $cat = 'accounting';

    /**
     * Model Xero Api Endpoint
     *
     * @var string
     */
    protected $endpoint = 'Invoices';

    /**
     * Model UUID like "Primary key"
     *
     * @var string
     */
    protected $id = 'InvoiceID';

    /**
     * Describe model attributes
     *
     * Every element contains either an array or type of the attribute
     * By default all attributes are requested from the API, but only the ones which have
     * ['post'] option will be sent to server
     *
     * ['required'] - needed for model validation will indicate that the attribute is required to POST\PUT the model
     *
     * ['type'] - attribute type
     *
     * Available types:
     *
     *  guid - model uuid, primary key
     *  string - string type
     *  float - float type
     *  int - integer type
     *  boolean - boolean type
     *  array - array type //TODO remove the array type at all, add models for all possible scenarios to replace array type
     *  date - string date, converted to carbon instance
     *  net-date - .NET date serialization present in JSON responses from API, converted to Carbon instance
     *  XeroModel descendant - if the attribute is another model, class name should be used as type
     *
     * ['collection, collectable'] - applicable to XeroModel descendant attributes, if API returns the attribute as a collection of models
     *
     * @var array
     */
    protected $modelAttributes = [

        //GUID resource ID

        'InvoiceID' => [
            'type' => 'guid',
        ],

        //Required to POST a draft Invoice

        'Type' => [
            'type' => 'string',
            'post',
            'required',
        ],

        'Contact' => [
            'type' => Contact::class,
            'post',
            'required',
        ],

        'LineItems' => [
            'type' => LineItem::class,
            'post',
            'required',
            'collection'
        ],

        //Recommended for POST/PUT requests

        //'DateString' => 'date', //We don't have to use it since .Net date is parsed anyway

        'Date' => [
            'type' => 'net-date',
            'post',
        ],

        //'DueDateString' => 'date', //We don't have to use it since .Net date is parsed anyway

        'DueDate' => [
            'type' => 'net-date',
            'post',
        ],

        'LineAmountTypes' => [
            'type' => 'string',
            'post',
        ],

        //Optional for PUT/POST requests

        'InvoiceNumber' => [
            'type' => 'string',
            'post',
        ],

        'Reference' => [
            'type' => 'string',
            'post',
        ],

        'BrandingThemeID' => [
            'type' => 'string',
            'post',
        ],

        'Url' => [
            'type' => 'string',
            'post',
        ],

        'CurrencyCode' => [
            'type' => 'string',
            'post',
        ],

        'CurrencyRate' => [
            'type' => 'float',
            'post',
        ],

        'Status' => [
            'type' => 'string',
            'post',
        ],

        'SentToContact' => [
            'type' => 'boolean',
            'post',
        ],

        'ExpectedPaymentDate' => [
            'type' => 'boolean',
            'post',
        ],

        'PlannedPaymentDate' => [
            'type' => 'boolean',
            'post',
        ],

        //Items returned on GET requests

        'SubTotal' => 'float',

        'TotalTax' => 'float',

        'Total' => 'float',

        'TotalDiscount' => 'float',

        'IsDiscounted' => 'boolean', //Undocumented

        'HasAttachments' => 'boolean',

        'Payments' => [
            'type' => InvoicePayment::class,
            'collection'
        ],

        'Prepayments' => [
            'type' => Prepayment::class,
            'collection'
        ],

        'Overpayments' => [
            'type' => Overpayment::class,
            'collection'
        ],

        'AmountDue' => 'float',

        'AmountPaid' => 'float',

        'FullyPaidOnDate' => 'net-date',

        'AmountCredited' => 'float',

        'CreditNotes' => [
            'type' => CreditNote::class,
            'collection'
        ]

    ];

}