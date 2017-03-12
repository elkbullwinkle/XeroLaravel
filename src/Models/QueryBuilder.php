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
use Elkbullwinkle\XeroLaravel\Models\Traits\ToArray;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToXml;
use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ReflectionClass;
use DOMDocument;
use Closure;

class QueryBuilder
{
    /**
     * @var XeroModel
     */
    protected $model = null;

    /**
     * Indicates if this is top level query
     *
     * @var boolean
     */

    protected $topLevel;

    /**
     * Indicates whether it is AND or OR expression
     *
     * @var boolean
     */
    protected $isAnd = true;

    /**
     * Variable that contains query
     *
     * @var array
     */
    protected $query = [];

    /**
     * Order attribute
     *
     * @var string
     */
    protected $orderAttribute = null;

    /**
     * Modified after Carbon instance or null if not specified
     *
     * @var Carbon|null
     */
    protected $modifiedAfter = null;

    public function __construct(XeroModel &$model, $topLevel = false)
    {
        $this->model = &$model;

        $this->topLevel = $topLevel;
    }

    //What we need is to describe


    //Retrieve query array

    /**
     * Get model query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set whether this is AND or OR query
     *
     * @param bool $and Conjunction type true = AND, false = OR
     */
    public function setConjunction($and = true)
    {
        $this->isAnd = $and;
    }

    /**
     * Get whether this is AND or OR query
     *
     * @return boolean
     */
    public function getConjunction()
    {
        return $this->isAnd;
    }

    //New instance of Query to accommodate nested queries

    protected function newQueryInstance()
    {
        return new static($this->model);
    }

    protected function addNestedQuery(QueryBuilder $query, $and = true)
    {
        $query->setConjunction($and);

        $this->query[] = $query;
    }

    //Where function

    public function where($attribute, $operator = null, $value = null, $and = true)
    {
        if ($attribute instanceof Closure)
        {
            $builder = $this->newQueryInstance();

            $attribute($builder);

            $this->addNestedQuery($builder, $and);
        }
        else
        {
            $this->processWhere(...func_get_args());
        }

        return $this->topLevel ? $this->model : $this;
    }

    //Or where function

    public function orWhere($attribute, $operator = null, $value = null)
    {
        if (func_num_args() == 2)
        {
            $value = $operator;
            $operator = '==';
        }

        if ($operator == '=') {
            $operator = '==';
        }

        return $this->where($attribute, $operator, $value, false);
    }

    public function orderBy($attribute, $descending = false)
    {
        //If this is executed from a closure we're just ignoring it
        if (!$this->topLevel)
        {
            return $this;
        }

        $type = $this->validateAttribute($attribute);

        if (is_bool($descending) === true)
        {
            $asc = !$descending;
        }
        else
        {
            $asc = !(strtolower($descending) == 'desc' || strtolower($descending) == 'descending');
        }

        $this->orderAttribute = [$attribute, $type, $asc];

        return $this->model;

    }

    public function modifiedAfter($date)
    {
        //If this is a nested query we're just ignoring it and returning query builder instance
        if (!$this->topLevel)
        {
            return $this;
        }

        if ($date instanceof Carbon)
        {
            $this->modifiedAfter = $date;
        }

        if (is_string($date))
        {
            $this->modifiedAfter = new Carbon($date);
        }

        return $this->model;
    }

    protected function processWhere($attribute, $operator = null, $value = null, $and = true)
    {

        $type = $this->validateAttribute($attribute);

        if (func_num_args() == 2)
        {
            $value = $operator;
            $operator = '==';
        }

        if ($operator == '=') {
            $operator = '==';
        }

        $this->validateOperator($operator, $type);

        switch ($type)
        {

            case 'string':
                str_replace('"', "'", $value);

                break;

            case 'float':
                $value = floatval($value);
                break;

            case 'int':
                $value = intval($value);
                break;

            case 'boolean':
                $value = boolval($value) ? 'true' : 'false';
                break;

            case 'net-date':
                $type = 'date';

            case 'date':
                if (!($value instanceof Carbon))
                {
                    $value = new Carbon($value);
                }

                break;

            case 'guid':
                str_replace('"', "", $value);
                str_replace("'", "", $value);
                break;

        }

        $this->query[] = [$attribute, $type, $operator, $value, $and];
    }

    protected function validateOperator($operator, $type)
    {
        if ($operator == '!=') $operator = '==';
        if ($operator == '<=') $operator = '>=';
        if ($operator == '<') $operator = '>';

        $availableOperators = [
            '==' => ['string', 'guid', 'date', 'net-date', 'boolean', 'float', 'int'],
            '>=' => ['float', 'int', 'date', 'net-date'],
            '>' => ['float', 'int', 'date', 'net-date'],
            'Contains' => ['string'],
            'StartsWith' => ['string'],
            'EndsWith' => ['string'],
            '!Contains' => ['string'],
            '!StartsWith' => ['string'],
            '!EndsWith' => ['string'],
        ];

        if (!in_array($operator, array_keys($availableOperators)))
        {
            throw new AttributeValidationException("Operator ${operator} is not valid");
        }

        if (!in_array($type, $availableOperators[$operator]))
        {
            throw new AttributeValidationException("Can not use operator ${operator} with ${type} type");
        }

    }

    /**
     * Validate attribute and return its type
     *
     * @param string $attribute Attribute name
     * @return string Attribute type
     * @throws AttributeValidationException
     */
    protected function validateAttribute($attribute)
    {
        //Accommodating querying attributes using dot notation

        if (str_contains($attribute, '.'))
        {
            $attributes = explode('.', $attribute);

            //Validate syntax
            if (!isset($attributes[0]) || !isset($attributes[1]))
            {
                throw new AttributeValidationException("Can not validate model query attribute");
            }

            //Validate nesting level
            if (count($attributes) > 2)
            {
                throw new AttributeValidationException("Querying nested nested attributes is not supported");
            }

            //Validate attribute exists
            if (is_null($modelAttribute = $this->model->getModelAttribute($attributes[0])))
            {
                throw new AttributeValidationException("Attribute ${attributes[0]} not found on this model");
            }

            if (!$this->model->isModelAttributeChildClass($attributes[0], false))
            {
                throw new AttributeValidationException("Attribute must be a class and not a collection, when using dot notation");
            }

            if (is_null($childModelAttribute = $modelAttribute['type']::_getModelAttribute($attributes[1])))
            {
                throw new AttributeValidationException("Attribute ${attributes[0]} not found on the child model");
            }

            if ($modelAttribute['type']::_isModelAttributeChildClass($attributes, null))
            {
                throw new AttributeValidationException("Child attribute must be a scalar property");
            }

            return $childModelAttribute['type'];
        }
        else
        {
            if (is_null($modelAttribute = $this->model->getModelAttribute($attribute)))
            {
                throw new AttributeValidationException("Model attribute \"${attribute}\" not found on this model");
            }

            if ($this->model->isModelAttributeChildClass($attribute))
            {
                throw new AttributeValidationException("Model attribute ${attribute} must be a scalar type");
            }

            return $modelAttribute['type'];
        }

    }

    protected function compileAttributeQuery($query)
    {
        $attribute = $query[0];
        $type = $query[1];
        $operator = $query[2];
        $value = $query[3];
        //$conjunction = $noConjunction ? '' : $query[4] ? ' AND ' : ' OR ';

        //First prepare the value according to the type, assuming that it has already been sanitized
        switch ($type)
        {
            case 'string':
                $value = "\"${value}\"";
                break;

            case 'date':
                $date = $value;
                $value = sprintf('DateTime(%d,%d,%d)', $date->year, $date->month, $date->day);
                break;

            case 'guid':
                $value = "Guid(\"${value}\")";
                break;
        }

        switch ($operator)
        {
            default:
                $expression = "${attribute} ${operator} ${value}";
                break;

            case 'Contains':
            case 'StartsWith':
            case 'EndsWith':
                $expression = "${attribute}.${operator}(${value})";
                break;

            case '!Contains':
            case '!StartsWith':
            case '!EndsWith':
                $expression = "!${attribute}." . substr($operator,1) . "(${value})";
                break;
        }

        return $expression;
    }

    public function compile()
    {
        $output = '';


        $isFirst = true;

        if (empty($this->query))
        {
            return null;
        }

        foreach ($this->query as $line)
        {

            $chunk = '';

            if (is_array($line))
            {
                $chunk = $this->compileAttributeQuery($line, true);

                $and = $line[4];

            }

            if ($line instanceof QueryBuilder)
            {
                $chunk = $line->compile();

                if (is_null($chunk)) {
                    continue;
                }

                $and = $line->getConjunction();
            }

            if ($isFirst) {
                $isFirst = false;
            }
            else
            {
                $chunk = ($and ? ' AND ' : ' OR ') . $chunk;
            }

            $output .= $chunk;
        }

        //If this is not a top level query builder we need to add parenthesis

        if (!$this->topLevel)
        {
            $output = "(${output})";
        }

        return $output;

    }

    public function compileOrderBy()
    {
        if (is_null($this->orderAttribute))
        {
            return null;
        }

        if (!$this->orderAttribute[2])
        {
            $sort = ' DESC';
        }
        else
        {
            $sort = '';
        }

        return $this->orderAttribute[0].$sort;
    }

    public function compileModifiedAfter()
    {
        if (is_null($this->modifiedAfter))
        {
            return null;
        }

        return $this->modifiedAfter->format('Y-m-d\TH:m:s');
    }

    /**
     * Convert the query to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->compile();
    }

}