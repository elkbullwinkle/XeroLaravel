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

class XeroCollection extends Collection
{
    /**
     * @var XeroModel
     */
    protected $model;

    /**
     * Determine whether model has been returned as paginated
     *
     * @var bool
     */
    protected $paginated = false;

    /**
     * @var int
     */
    protected $page = 1;

    protected $type = '';

    /**
     * @var XeroModel
     */
    protected $parent = null;

    public function __construct($items = [])
    {
        parent::__construct($items);

    }

    public function setParent(XeroModel &$model)
    {
        $this->parent = &$model;

        return $this;
    }

    public function setModel(&$model)
    {
        $this->model = &$model;

        return $this;
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function setType($type)
    {
        if (!is_a($type, XeroModel::class, true))
        {
            throw new \Exception("${type} must be a subclass of XeroModel class");
        }

        $this->type = $type;

        return $this;
    }

    public function setPaginated($paginated)
    {
        $this->paginated = $paginated;

        return $this;
    }

    /**
     * @return XeroModel|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function fetchNextPage($append = false)
    {
        if (!$this->paginated)
        {
            return $this;
        }

        $this->page +=1;

        $array = $this->fetchPage($this->page);

        if ($append)
        {
            $this->items = array_merge($this->items, $array);
        }
        else
        {
            $this->items = $array;
        }

        return $this;
    }

    public function fetchPrevPage($prepend = false)
    {
        if (!$this->paginated || $this->page <= 1)
        {
            return $this;
        }

        $this->page -=1;

        $array = $this->fetchPage($this->page);

        if ($prepend)
        {
            $this->items = $array;
        }
        else
        {
            $this->items = array_merge($array, $this->items);
        }

        return $this;
    }

    protected function fetchPage($page)
    {
        if (!(is_a($this->model, XeroModel::class)))
        {
            return [];
        }

        return $this->model->retrieveModelArray(true, $page);
    }

    public function add(XeroModel $model)
    {
        if (!is_a($model, $this->type))
        {
            throw new \Exception("Child model must be an instance of {$this->type}");
        }
        else
        {
            $model->setParent($this->model);
            array_push($this->items, $model);
        }

        return $this;
    }

    public function create($attributes = [])
    {
        $model = ($this->type)::createFromJson($attributes, $this->model->getConnection())
            ->setParent($this->parent);

        array_push($this->items, $model);

        return $this;
    }
}