<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Contact extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'Contacts';

    protected $id = 'ContactID';

    protected $required = [
        'Name',
    ];

    protected $collections = [
        'ContactPersons',
        'Addresses',
        'Phones',
        'SalesTrackingCategories',
        'PurchasesTrackingCategories',
    ];

    protected $attrs = [
        'ContactID' => 'guid',

        'ContactNumber' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'AccountNumber' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'ContactStatus' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'Name' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'FirstName' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'LastName' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'EmailAddress' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'SkypeUserName' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'ContactPersons' => [
            'type' => ContactPerson::class,
            'post',
            'put',
        ],

        'BankAccountDetails' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'TaxNumber' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'AccountsReceivableTaxType' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'AccountsPayableTaxType' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'Addresses' => [
            'type' => Address::class,
            'post',
            'put',
        ],

        'Phones' => [
            'type' => Phone::class,
            'post',
            'put',
        ],

        'IsSupplier' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'IsCustomer' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'DefaultCurrency' => [
            'type' => 'string',
            'post',
            'put',
        ],

        //Specific


        'XeroNetworkKey' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'SalesDefaultAccountCode' => [
            'type' => Account::class,
            'post',
            'put',
        ],

        'PurchasesDefaultAccountCode' => [
            'type' => Account::class,
            'post',
            'put',
        ],

        'SalesTrackingCategories' => [
            'type' => TrackingCategory::class,
            'post',
            'put',
        ],

        'PurchasesTrackingCategories' => [
            'type' => TrackingCategory::class,
            'post',
            'put',
        ],

        'TrackingCategoryName' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'TrackingCategoryOption' => [
            'type' => 'string',
            'post',
            'put',
        ],

        'PaymentTerms' => [
            'type' => 'string',
            'post',
            'put',
        ],

        //GET Only


        //'ContactGroups' => 'string', //Unsure regarding type

        'Website' => 'string',

        'BrandingTheme' => BrandingTheme::class,

        //'BatchPayments' => 'int', //Unsure about type

        'Discount' => 'float',

        'Balances' => 'string',

        'HasAttachments' => 'boolean',

    ];
}