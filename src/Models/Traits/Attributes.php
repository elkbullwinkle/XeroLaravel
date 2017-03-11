<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;

trait Attributes {

    /**
     * Properties (fields) of the model
     *
     * @var array
     */
    protected $attributes;

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
    protected $modelAttributes = [];

    /**
     * Describe shared model attributes, similar to model attributes, but shared across all the models
     *
     * @var array
     */
    protected $sharedAttributes = [
        'UpdatedDateUTC' => 'net-date',
        'HasErrors' => 'boolean',
        'HasAttachments' => 'boolean',
    ];

    /**
     * List of attributes which were pulled from Xero API during GET request, but weren't described in modelAttributes
     * or sharedAttributes array
     *
     * @var array
     */
    protected $ignoredAttributes = [];

    /**
     * Return model attribute if exists (always returns attribute as a complex type, e.g. in array form
     *
     * @param string $attributeName Attribute name
     * @return null|array
     */
    public function getModelAttribute($attributeName)
    {

        if (!$this->modelAttributeExists($attributeName))
        {
            return null;
        }

        if (is_array($attribute = $this->getAllModelAttributes()[$attributeName]))
        {
            return $attribute;
        }
        else
        {
            return [
                'type' => $attribute,
            ];
        }

    }

    /**
     * Determine whether the given attribute should be a collection
     *
     * @param string $attributeName Attribute name
     * @return bool|null
     */
    public function isModelAttributeCollectable($attributeName)
    {
        if (is_null($attribute = $this->getModelAttribute($attributeName)))
        {
            return null;
        }

        return in_array('collection', $attribute);
    }

    /**
     * Check if given model attribute exists
     *
     * @param string $attributeName Attribute name
     * @return bool
     */
    public function modelAttributeExists($attributeName)
    {
        return in_array($attributeName, array_keys($this->getAllModelAttributes()));
    }

    /**
     * Check is a model attribute a class and whether it is a collection
     *
     * if $collectable = null returns true if the attribute is a class or collection
     * if $collectable = true returns true if the attribute is a collection
     * if $collectable = false return true if the attribute is a class
     *
     * @param $attributeName
     * @param bool|null $collectable Should child attribute be a collection
     * @return boolean|null
     */
    public function isModelAttributeChildClass($attributeName, $collectable = null)
    {
        if (is_null($attribute = $this->getModelAttribute($attributeName)))
        {
            return null;
        }

        if (is_a($attribute['type'], XeroModel::class, true))
        {
            if (is_null($collectable))
            {
                return true;
            }

            return ($collectable && $this->isModelAttributeCollectable($attributeName)) ||
                (!$collectable && !$this->isModelAttributeCollectable($attributeName));
        }

        return false;
    }

    /**
     * Return model attribute type by given name
     *
     * @param string $attributeName Attribute name
     * @return string|null
     */
    public function getModelAttributeType($attributeName)
    {
        if (!$this->modelAttributeExists($attributeName))
        {
            return null;
        }

        return $this->getModelAttribute($attributeName)['type'];
    }

    /**
     * Return all model attributes
     *
     * @param bool $includeShared Flag to include shared attributes
     * @return array
     */
    public function getAllModelAttributes($includeShared = true)
    {
        if ($includeShared)
        {
            return array_merge($this->sharedAttributes, $this->modelAttributes);
        }

        return $this->modelAttributes;
    }


    /**
     * Retrieve a model attribute
     *
     * @param string $name
     * @return XeroModel|Collection|Carbon|string|float|bool|array
     */
    public function __get($name)
    {

        //Add some special attributes if needed

        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
    }

    /**
     * Set a model attribute
     *
     * @param string $name
     * @param XeroModel|Collection|Carbon|string|float|bool|array $value
     */
    public function __set($name, $value)
    {

    }

}