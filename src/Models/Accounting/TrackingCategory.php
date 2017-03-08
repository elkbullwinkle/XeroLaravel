<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class TrackingCategory extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'TrackingCategories';

    protected $id = 'TrackingCategoryID';

    protected $required = [
        'Name',
    ];

    protected $collections = [
        'Options'
    ];

    protected $attrs = [

        'TrackingCategoryID' => [
            'type' => 'guid',
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

        'Status' => [
            'type' => 'string',
            'put',
            'post',
        ],

        'Options' => [
            'type' => TrackingOption::class,
        ],

    ];
}