<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Exceptions\AttributeValidationException;
use Elkbullwinkle\XeroLaravel\Models\QueryBuilder;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;
use Mockery\Matcher\Closure;

trait FluentQueries {

    /**
     * Query builder instance, initialized when the model is instantiated
     *
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * Return query builder instance
     *
     * @return QueryBuilder|null
     */
    public function getBuilderInstance()
    {
        return $this->builder;
    }

    //Function to build final query from array to recursively process query array

    protected function getAttributeFromWhereOrOrder(&$name)
    {
        $attribute = $name;

        if (starts_with($name, ['orWhere', 'where', 'orderBy']))
        {
            $attribute = str_replace(['orWhere', 'where', 'orderBy'], '', $attribute);

            $name = str_replace($attribute, '', $name);

        }

        return $attribute;
    }

    //Two magic functions which would describe call to not defined function on the class
    //Or call not defined static function on the class

    /**
     * Handling dynamic calls to the model
     *
     * @param $name
     * @param $arguments
     * @throws AttributeValidationException
     * @return $this
     */
    public function __call($name, $arguments)
    {
        //Setting connection

        switch ($name)
        {
            default:
                if (starts_with($name, ['where', 'orWhere', 'orderBy', 'modifiedAfter']))
                {
                    if (count($arguments) < 1)
                    {
                        throw new AttributeValidationException('Not enough arguments supplied');
                    }

                    if (!in_array($name, ['where', 'orWhere', 'orderBy', 'modifiedAfter']))
                    {
                        //$attribute = array_shift($arguments);
                        $attribute = $this->getAttributeFromWhereOrOrder($name);

                        array_unshift($arguments, $attribute);

                    }

                    return $this->builder->$name(...$arguments);
                }

                return $this;

            case 'setConnection':
                return $this->setConnection(...$arguments);

            case 'get':
                return $this->retrieveModelCollection(...$arguments);

            case 'all':
                return $this->retrieveAllPaginated(...$arguments);
        }

    }

    /**
     * Handling dynamic static calls to the model
     *
     * @param $name
     * @param $arguments
     * @return static
     */
    public static function __callStatic($name, $arguments)
    {

        $null = null;

        switch ($name)
        {
            default:
                if (starts_with($name, ['where', 'orWhere', 'orderBy', 'modifiedAfter']))
                {
                    return (new static)->$name(...$arguments);
                }

                return new static();

            case 'get':
                return (new static)->retrieveModelCollection(...$arguments);

            case 'all':
                return (new static)->retrieveAllPaginated(...$arguments);

            case 'setConnection':
                return new static(...$arguments);

            case '_getAllModelAttributes':
                return (new static($null))->getAllModelAttributes(...$arguments);

            case '_getModelAttribute':
                return (new static($null))->getModelAttribute(...$arguments);

            case '_isModelAttributeChildClass':
                return (new static($null))->isModelAttributeChildClass(...$arguments);
        }

    }

}