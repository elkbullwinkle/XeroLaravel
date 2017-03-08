<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class Payment extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'Payments';

    protected $id = 'PaymentID';

    protected $required = [
        'Date',
        'Amount',
        'InvoiceID',
    ];

    protected $attrs = [
        'PaymentID' => 'guid',
        'Date' => 'net-date',
        'Amount' => 'float',
        'Reference' => 'string',
        'CurrencyRate' => 'int',
        'HasAccount' => 'boolean',
        'HasValidationErrors' => 'boolean',
    ];
}