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
    /**
     * Determine whether it is only child model or can it be fetched directly from the API
     *
     * @var bool
     */
    protected $fetchable = false;

    /**
     * Override default model name
     * This is required for the sake of posting or updating parent model
     * To fit the format of the XML required by Xero
     *
     * @var string
     */
    protected $overrideName = 'TrackingCategory';

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

        'TrackingCategoryID' => [
            'type' => 'string',
            'post'
        ],

        'TrackingOptionID' => [
            'type' => 'string',
            'post'
        ],

        'Name' => [
            'type' => 'string',
            'post',
        ],

        'Option' => [
            'type' => 'string',
            'post',
        ],

    ];
}