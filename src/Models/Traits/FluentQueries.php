<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\QueryBuilder;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;
use Mockery\Matcher\Closure;

trait FluentQueries {

    //Builder variable

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

    //Two magic functions which would describe call to not defined function on the class
    //Or call not defined static function on the class

    public function __call($name, $arguments)
    {
        //Setting connection

        switch ($name)
        {
            default:
                if (starts_with($name, ['where', 'orWhere']))
                {
                    return $this->builder->$name(...$arguments);
                }

                return $this;

            case 'setConnection':
                return $this->setConnection(...$arguments);
        }



    }

    public static function __callStatic($name, $arguments)
    {
        //Setting connection

        switch ($name)
        {
            default:
                if (starts_with($name, ['where', 'orWhere']))
                {
                    return (new static)->$name(...$arguments);
                }

                return new static();

            case 'setConnection':
                return new static(...$arguments);

            case '_getAllModelAttributes':
                return (new static)->getAllModelAttributes($arguments[0]);

            case '_getModelAttribute':
                return (new static)->getModelAttribute($arguments[0]);

            case '_isModelAttributeChildClass':
                return (new static)->isModelAttributeChildClass(...$arguments);
        }

    }

}