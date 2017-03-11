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
use Elkbullwinkle\XeroLaravel\Models\Traits\ToArray;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToXml;
use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ReflectionClass;
use DOMDocument;

abstract class XeroModel extends Fluent implements Arrayable
{
    use ToArray, ToXml, Attributes, FluentQueries;

    protected $pageable = false;

    protected $fetchable = true;

    protected $dependsOn = null;

    protected $overrideName = null;

    protected $parent = null;

    /**
     * @var XeroLaravel
     */
    protected $connection;



    protected $guid = null;

    protected $cat = null;

    protected $endpoint = '';



    public $lastError = [
        'code' => '',
        'error' => '',
    ];



    public function __construct()
    {
        $this->connection = resolve('XeroLaravel')->setModel($this);

    }

    public function getLastError()
    {
        return $this->lastError;
    }



    /**
     * @return XeroLaravel
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return bool
     */
    public function isPageable()
    {
        return $this->pageable;
    }



    public static function createFromJson($json)
    {

        $model = new static;

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
     * @return Collection
     */
    public static function createCollectionFromJson($json)
    {
        //Okay we need collection

        $collection = [];

        foreach ($json as $model)
        {
            $collection[] = static::createFromJson($model);
        }

        return collect($collection);
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
            throw new \Exception('The child must be a subclass of XeroModel');
        }

        return $class::createFromJson($attribute)
            ->setParent($this);
    }

    protected function processChildrenClasses($name, $attribute, $class)
    {
        $collection =  $class::createCollectionFromJson($attribute);

        $collection->transform(function($item) {
            return $item->setParent($this);
        });

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

    public function isDirty()
    {

    }

    public function canBeSaved()
    {
        return $this->fetchable;
    }

    public function validate()
    {

    }


    protected function validateAttribute($name, $value)
    {
        if (in_array($name, array_keys($attrs = array_merge($this->sharedAttrs, $this->attrs))))
        {
            /*
             * Possible types of attributes:
             *
             * String
             * Float
             * Int
             * Boolean
             * Array
             * Carbon
             * XeroModel descendant
             * Collection of XeroModels
             *
             */

            $isCollection = in_array($name, $this->collections);

            if (is_array($attrs[$name]))
            {
                $type = $attrs[$name]['type'];

                if (in_array('collection',$attrs['name']))
                {
                    $isCollection = true;
                }
            }
            else
            {
                $type = $attrs[$name];
            }


            switch (strtolower($type))
            {
                default:

                    //Check for collections

                    if ($value instanceof Collection)
                    {
                        if ($value->isEmpty())
                        {
                            return $value;
                        }

                        else

                        {
                            //Need to validate that every element of collection has a correct type

                            $filtered = $value->filter(function($value) use ($type) {
                                return is_a($value, $type);
                            });

                            return $filtered;
                        }
                    }

                    if (is_a($value, $type))
                    {

                    }

                    break;

                case 'string':
                    return (string) $value;

                case 'float':
                    return floatval($value);

                case 'int':
                    return intval($value);

                case 'boolean':
                    return boolval($value);
                case 'array':

                    if (!is_array($value))
                    {
                        throw new AttributeValidationException("Attribute {$name} must be an array. " . gettype($value) . " given");
                    }

                    return $value;

                case 'date':
                case 'net-date':

                    if (gettype($value) == 'string')
                    {
                        return new Carbon($value);
                    }
                    else
                    {
                        if ($value instanceof Carbon)
                        {
                            return $value;
                        }
                        else
                        {
                            throw new AttributeValidationException("Attribute {$name} must be an a Carbon instance or valid date-time string. " . gettype($value) . " given");
                        }
                    }
            }
        }
    }



    public function save()
    {
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->toXml());
        echo $dom->saveXML();
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