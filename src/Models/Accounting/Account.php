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
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'Accounts';

    protected $id = 'AccountID';

    protected $required = [
        'Code',
        'Name',
        'Type',
    ];

    protected $attrs = [

        'Code' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'Name' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'Type' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'BankAccountNumber' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'Status' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'Description' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'BankAccountType' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'CurrencyCode' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'TaxType' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'EnablePaymentsToAccount' => [
            'type' => 'boolean',
            'post',
            'put'
        ],

        'ShowInExpenseClaims' => [
            'type' => 'boolean',
            'post',
            'put'
        ],

        //Read only retrieved on GET requests

        'AccountID' => 'guid',

        'Class' => 'net-date',

        'SystemAccount' => 'float',

        'ReportingCode' => 'string',

        'ReportingCodeName' => 'int',

        'HasAttachments' => 'boolean',

    ];
}