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
        // TODO: Implement __call() method.
    }


    public function __callStatic($name, $arguments)
    {
        if ($name == 'getAllModelAttributes')
        {
            $model = new static;
            return $model->getAllModelAttributes();
        }

        if ($name == 'getModelAttribute')
        {
            $model = new static;
            return $model->getModelAttribute($arguments[0]);
        }

        if ($name == 'isModelAttributeChildClass')
        {
            $model = new static;
            return $model->isModelAttributeChildClass(...$arguments);
        }

        // TODO: Implement __callStatic() method.
    }

}