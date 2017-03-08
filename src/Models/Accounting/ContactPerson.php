<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class ContactPerson extends XeroModel
{
    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = '';

    protected $id = '';

    protected $required = [
        'FirstName',
        'LastName',
    ];

    protected $attrs = [

        'FirstName' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'LastName' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'EmailAddress' => [
            'type' => 'string',
            'post',
            'put'
        ],

        'IncludeInEmails' => [
            'type' => 'boolean',
            'post',
            'put'
        ],

    ];
}