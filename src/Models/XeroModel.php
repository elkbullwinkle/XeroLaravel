<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 5:53 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Exceptions\AttributeValidationException;
use Elkbullwinkle\XeroLaravel\Models\Traits\Attributes;
use Elkbullwinkle\XeroLaravel\Models\Traits\FluentQueries;
use Elkbullwinkle\XeroLaravel\Models\Traits\Postable;
use Elkbullwinkle\XeroLaravel\Models\Traits\Retrievable;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToArray;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToXml;
use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

abstract class XeroModel implements Arrayable
{
    use ToArray,
        ToXml,
        Attributes,
        Retrievable,
        Postable,
        FluentQueries;

    protected $dependsOn = null;

    protected $overrideName = null;

    protected $parent = null;

    protected $guid = null;

    protected $cat = null;

    protected $endpoint = '';

    public $lastError = [
        'code' => '',
        'error' => '',
    ];


    public function __construct($connection = 'default')
    {
        if (!is_null($connection))
        {
            $this->setConnection($connection);
        }

        $this->builder = new QueryBuilder($this, true);

        return $this;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public static function createFromJson($json, $connection = 'default', XeroModel &$model = null)
    {

        if (!is_a($model, static::class))
        {
            $model = new static($connection);
        }

        foreach ($json as $name => $attributeValue)
        {

            if ($modelAttribute = $model->getModelAttribute($name))
            {
                //We don't want to add empty attributes, by default if a Xero API request is made using
                //xml it doesn't return empty elements, but with json it does
                if ((is_string($attributeValue) && trim($attributeValue) == ''))
                {
                    continue;
                }

                $processedAttribute = $model->processAttribute($name, $attributeValue, $modelAttribute);

                //We don't want to add empty collections, by default if a Xero API request is made using
                //xml it doesn't return empty elements, but with json it does
                if (is_a($processedAttribute, Collection::class) && $processedAttribute->isEmpty())
                {
                    continue;
                }

                $model->attributes[$name] = $processedAttribute;

            }

        }

        return $model;

    }

    /**
     * Build a collection of models using decoded JSON string
     *
     * @param array $json Decoded JSON string returned from Xero
     * @param string $connection Set connection for the created model
     * @param boolean $returnArray Whether should return array flag
     * @return Collection
     */
    public static function createCollectionFromJson($json, &$connection = 'default', $returnArray = false)
    {
        //Okay we need collection

        $collection = [];

        foreach ($json as $model)
        {
            $collection[] = static::createFromJson($model, $connection);
        }

        if ($returnArray)
        {
            return $collection;
        }

        $collection = new XeroCollection($collection);

        if ($connection instanceof XeroLaravel)
        {
            $model = $connection->getModel();
            $collection->setModel($model);
        }

        return $collection;
    }


    protected function processAttribute($name, $value, $attribute)
    {
        $type = $attribute['type'];

        switch (strtolower($type))
        {
            default:

                if ($this->isModelAttributeCollectable($name))
                {
                    return $this->processChildrenClasses($name, $value, $type);
                }

                return $this->processChildClass($name, $value, $type);

            case 'string':
                return $value;

            case 'guid':
                $this->guid = $value;
                return $this->guid;

            case 'boolean':
                return (boolean) $value;

            case 'float':
                return floatval($value);

            case 'int':
                return intval($value);

            case 'date':
                return new Carbon($value);

            case 'net-date':
                return $this->processNetDate($value);

            case 'array':
                return $value;
        }
    }

    /**
     * Set parent XeroModel
     *
     * @param XeroModel $parentModel Parent model
     * @return $this
     */
    public function setParent(XeroModel &$parentModel)
    {
        $this->parent = &$parentModel;

        return $this;
    }

    /**
     * Retrieve parent Xero model
     *
     * @return XeroModel|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    protected function processChildClass($name, $attribute, $class)
    {

        if (!is_a($class, XeroModel::class, true))
        {
            throw new AttributeValidationException("${name} must be ${attribute['type']}");
        }

        return $class::createFromJson($attribute, $this->connection)
            ->setParent($this);
    }

    protected function processChildrenClasses($name, $attribute, $class)
    {
        $collection = $class::createCollectionFromJson($attribute, $this->connection);

        $collection->transform(function($item) {
            return $item->setParent($this);
        })->setParent($this)->setType($class);

        return $collection;
    }

    protected function processNetDate($netDate)
    {
        preg_match( '/\/Date\((?<ts>-?\d+)(?<tz>[+-]\d{4})\)/', $netDate, $matches);

        $time = intval($matches['ts']/1000);

        if (isset($matches['tz']))
        {
            $time += intval($matches['tz'] / 100 * 60 * 60);
        }

        return Carbon::createFromTimestamp($time);
    }


    /**
     * Get API endpoint for the current model
     *
     * @param bool $singular Should endpoint name be returned as singular
     * @return string
     */
    public function getEndpoint($singular = false)
    {
        return $singular ? str_singular($this->endpoint) : $this->endpoint;
    }

    public function getCat()
    {
        return $this->cat;
    }

    public function getOverriddenName()
    {
        return $this->overrideName;
    }
}