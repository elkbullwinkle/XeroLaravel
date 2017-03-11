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
    protected $class;

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
    }

    /**
     * @return XeroModel|null
     */
    public function getParent()
    {
        return $this->parent;
    }
}