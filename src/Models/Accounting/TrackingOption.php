<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class TrackingOption extends XeroModel
{
    protected $pageable = false;
    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'TrackingOptions';

    protected $dependsOn = TrackingCategory::class;

    protected $id = 'TrackingOptionID';

    protected $required = [
        'Name',
    ];

    protected $attrs = [

        'TrackingOptionID' => [
            'type' => 'guid',
        ],

        'Name' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'Status' => [
            'type' => 'string',
            'put',
            'post',
        ],

    ];
}