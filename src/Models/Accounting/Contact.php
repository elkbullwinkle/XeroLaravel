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
    protected $endpoint = 'Contacts';

    /**
     * Model UUID like "Primary key"
     *
     * @var string
     */
    protected $id = 'ContactID';

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
        'ContactID' => 'guid',

        'ContactNumber' => [
            'type' => 'string',
            'post',
        ],

        'AccountNumber' => [
            'type' => 'string',
            'post',
        ],

        'ContactStatus' => [
            'type' => 'string',
            'post',
        ],

        'Name' => [
            'type' => 'string',
            'post',
            'required'
        ],

        'FirstName' => [
            'type' => 'string',
            'post',
        ],

        'LastName' => [
            'type' => 'string',
            'post',
        ],

        'EmailAddress' => [
            'type' => 'string',
            'post',
        ],

        'SkypeUserName' => [
            'type' => 'string',
            'post',
        ],

        'ContactPersons' => [
            'type' => ContactPerson::class,
            'post',
            'collection'
        ],

        'BankAccountDetails' => [
            'type' => 'string',
            'post',
        ],

        'TaxNumber' => [
            'type' => 'string',
            'post',
        ],

        'AccountsReceivableTaxType' => [
            'type' => 'string',
            'post',
        ],

        'AccountsPayableTaxType' => [
            'type' => 'string',
            'post',
        ],

        'Addresses' => [
            'type' => Address::class,
            'post',
            'collection'
        ],

        'Phones' => [
            'type' => Phone::class,
            'post',
            'collection'
        ],

        'IsSupplier' => [
            'type' => 'boolean',
            'post',
        ],

        'IsCustomer' => [
            'type' => 'boolean',
            'post',
        ],

        'DefaultCurrency' => [
            'type' => 'string',
            'post',
        ],

        //Specific


        'XeroNetworkKey' => [
            'type' => 'string',
            'post',
        ],

        'SalesDefaultAccountCode' => [
            'type' => 'string',
            'post',
        ],

        'PurchasesDefaultAccountCode' => [
            'type' => 'string',
            'post',
        ],

        'SalesTrackingCategories' => [
            'type' => ElementTracking::class,
            'post',
            'collection'
        ],

        'PurchasesTrackingCategories' => [
            'type' => ElementTracking::class,
            'post',
            'collection'
        ],

        'TrackingCategoryName' => [
            'type' => 'string',
            'post',
        ],

        'TrackingCategoryOption' => [
            'type' => 'string',
            'post',
        ],

        'PaymentTerms' => [
            'type' => 'string',
            'post',
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