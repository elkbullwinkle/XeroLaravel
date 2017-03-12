<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\QueryBuilder;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;
use Mockery\Matcher\Closure;

trait FluentQueries {

    //Builder variable

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
        if (starts_with($name, ['where', 'orWhere']))
        {
            return $this->builder->$name(...$arguments);
        }
    }


    public static function __callStatic($name, $arguments)
    {

        if ($name == '_getAllModelAttributes')
        {
            $model = new static;
            return $model->getAllModelAttributes();
        }

        if ($name == '_getModelAttribute')
        {
            $model = new static;
            return $model->getModelAttribute($arguments[0]);
        }

        if ($name == '_isModelAttributeChildClass')
        {
            $model = new static;
            return $model->isModelAttributeChildClass(...$arguments);
        }

        if (starts_with($name, ['where', 'orWhere']))
        {

            $model = new static;

            return $model->$name(...$arguments);
        }

        // TODO: Implement __callStatic() method.
    }

}