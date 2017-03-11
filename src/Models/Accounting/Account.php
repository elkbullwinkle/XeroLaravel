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
     * {@inheritdoc}
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