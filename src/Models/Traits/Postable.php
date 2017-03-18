<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Exceptions\AttributeValidationException;
use Elkbullwinkle\XeroLaravel\Models\XeroCollection;
use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use DOMDocument;

trait Postable {

    /**
     * Save the current model
     *
     * @return $this
     */
    public function save()
    {
        if (!$this->canBeSaved())
        {
            return $this;
        }

        if (is_a($this->parent, XeroModel::class))
        {
            $this->parent->save();

            return $this;
        }

        //Need to figure out is it a new model or not
        if ($this->exists())
        {
            //Updating model
            $result = $this->updateModel();
        }
        else
        {
            //Creating model
            $result = $this->createModel();
        }

        if ($result)
        {
            //We need to re-init the model from server response
            static::createFromJson(array_shift($result), $this->connection, $this);

            return true;
        }

        return false;
    }

    public function canBeSaved()
    {
        return $this->fetchable && (is_a($this->connection, XeroLaravel::class));
    }

    public function exists()
    {
        return !is_null($this->guid) && $this->fetchable;
    }

    public function validForCreation()
    {
        $requiredAttributes = $this->getRequiredModelAttributes();

        foreach ($requiredAttributes as $requiredAttribute)
        {
            if (!(isset($this->attributes[$requiredAttribute]) && !is_null($this->attributes[$requiredAttribute])))
            {
                return false;
            }
        }

        return true;
    }

    protected function createModel()
    {
        if (!$this->validForCreation())
        {
            throw new \Exception("This model is not valid for creation");
        }

        $xml = $this->toXml('create');

        //Ok to create a model we need to send a put request to the model endpoint and blablabla
        $result = $this->connection->put('?summarizeErrors=false', [
            'xml' => $xml
        ]);


        return $result;


    }

    protected function updateModel()
    {

        $xml = $this->toXml('update');

        //Ok to update a model we need to send a post request to the model endpoint and blablabla
        $result = $this->connection->post($this->guid, [
            'xml' => $xml
        ]);

        return $result;
    }

    public function isDirty($name = null)
    {
        if (is_null($name))
        {
            return !empty($this->dirtyAttributes);
        }

        return in_array($name, $this->dirtyAttributes);
    }

    protected function setDirty($name)
    {
        if ($modelAttribute = $this->getModelAttribute($name))
        {
            $this->dirtyAttributes[$name] = true;
        }

        return $this;
    }

    protected function saveAttribute($name, $value)
    {
        if ($modelAttribute = $this->getModelAttribute($name))
        {
            $this->setDirty($name);

            switch ($modelAttribute['type'])
            {
                default:
                    if (is_a($value, $modelAttribute['type']))
                    {
                        return $value;
                    }
                    if (is_a($value, XeroCollection::class))
                    {
                        return $this->saveChildrenCollection($name, $modelAttribute, $value);
                    }

                    if (is_array($value))
                    {
                        if (is_a($modelAttribute['type'], XeroModel::class, true))
                        {
                            return $modelAttribute['type']::createFromJson($value, null)->setParent($this);
                        }
                    }

                    throw new AttributeValidationException("Attribute \"${name}\" type is unknown");

                    return $value;

                case 'string':
                    return (string) $value;

                case 'guid':
                    $this->guid = (string) $value;
                    return (string) $value;

                case 'int':
                    return intval($value);

                case 'float':
                    return floatval($value);

                case 'boolean':
                    return boolval($value);

                case 'array':
                    if (!is_array($value))
                    {
                        throw new AttributeValidationException("Attribute ${name} must be an array");
                    }

                    return $value;

                case 'date':
                case 'net-date':
                    if (is_string($value))
                    {
                        return new Carbon($value);
                    }

                    if ($value instanceof Carbon)
                    {
                        return $value;
                    }

                    throw new AttributeValidationException("Attribute ${name} must be a valid datetime string or a Carbon instance");

                    return new Carbon();

            }

        }
        else
        {
            throw new AttributeValidationException("Attribute ${name} is not defined on ". static::class . " model");
        }

        return $value;
    }

    protected function saveChildrenCollection($name, $modelAttribute, $children)
    {
        foreach ($children as &$child)
        {
            if (!is_a($child, $modelAttribute['type']))
            {
                throw new AttributeValidationException("Child of ${name} collection must be an instance of ${modelAttribute['type']}");
            }

            $child->setParent($this)
                ->setConnection(null);

            $children->setType($modelAttribute['type']);
        }

        return $children;
    }

}