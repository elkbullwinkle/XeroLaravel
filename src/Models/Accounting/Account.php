<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Account extends XeroModel
{
    /**
     * Is collection pageable
     *
     * @var bool
     */
    protected $pageable = false;

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
    protected $endpoint = 'Accounts';

    /**
     * Model UUID like "Primary key"
     *
     * @var string
     */
    protected $id = 'AccountID';

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

        'Code' => [
            'type' => 'string',
            'post',
            'required'
        ],

        'Name' => [
            'type' => 'string',
            'post',
            'required'
        ],

        'Type' => [
            'type' => 'string',
            'post',
            'required'
        ],

        'BankAccountNumber' => [
            'type' => 'string',
            'post',
        ],

        'Status' => [
            'type' => 'string',
            'post',
        ],

        'Description' => [
            'type' => 'string',
            'post',
        ],

        'BankAccountType' => [
            'type' => 'string',
            'post',
        ],

        'CurrencyCode' => [
            'type' => 'string',
            'post',
        ],

        'TaxType' => [
            'type' => 'string',
            'post',
        ],

        'EnablePaymentsToAccount' => [
            'type' => 'boolean',
            'post',
        ],

        'ShowInExpenseClaims' => [
            'type' => 'boolean',
            'post',
        ],

        //Read only retrieved on GET requests

        'AccountID' => 'guid',

        'Class' => 'string',

        'SystemAccount' => 'string',

        'ReportingCode' => 'string',

        'ReportingCodeName' => 'string',

        'HasAttachments' => 'boolean',

    ];
}