<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;

trait FluentQueries {

    //Query variable

    /**
     * Variable that contains query
     *
     * @var array
     */
    protected $query = [];


    //What we need is to describe


    //Where function

    protected function where()
    {


        return $this;
    }


    //Or where function

    protected function orWhere()
    {


        return $this;
    }

    //Page function

    protected function retrievePage()
    {


        return $this;
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
        // TODO: Implement __callStatic() method.
    }

}