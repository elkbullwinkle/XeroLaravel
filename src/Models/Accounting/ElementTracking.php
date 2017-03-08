<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class ElementTracking extends XeroModel
{
    protected $pageable = false;
    protected $fetchable = false;

    protected $cat = 'accounting';

    protected $endpoint = '';

    protected $id = '';

    protected $overrideName = 'TrackingCategory';

    protected $required = [
    ];

    protected $collections = [
        'Options'
    ];

    protected $attrs = [

        'TrackingCategoryID' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'TrackingOptionID' => [
            'type' => 'string',
            'put',
            'post'
        ],

        'Name' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'Option' => [
            'type' => 'string',
            'put',
            'post',
        ],

    ];
}