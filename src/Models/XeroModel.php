<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 5:53 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToArray;
use Elkbullwinkle\XeroLaravel\Models\Traits\ToXml;
use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use DOMDocument;

abstract class XeroModel implements Arrayable
{
    use ToArray, ToXml;

    protected $pageable = false;

    protected $dependsOn = null;

    protected $overrideName = null;

    /**
     * @var XeroLaravel
     */
    protected $connection;

    protected $attributes;

    protected $cats = [
        'accounting' => 'api.xro'
    ];

    protected $guid = null;

    protected $version = '2.0';

    protected $sharedAttrs = [
        'UpdatedDateUTC' => 'net-date',
        'HasErrors' => 'boolean',
    ];

    protected $collections = [];

    protected $lastError = [
        'code' => '',
        'error' => '',
    ];

    protected $ignoredAttrs = [];

    public function __construct()
    {
        $this->connection = resolve('XeroLaravel');
    }

    /**
     * @return XeroLaravel
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return bool
     */
    public function isPageable()
    {
        return $this->pageable;
    }

    public function retrieveByGuid($guid) {

        $url = $this->buildEndpointUrl($this->connection->baseUrl);

        $response = $this->connection->transport->get($url.$guid);

        $this->processResponse($response);

        //var_dump($response);

        return $this;
    }

    protected function processResponse($response, $isCollection = false)
    {
        if (!$response['status'])
        {
            $this->lastError = [
                'code' => $response['code'],
                'error' => $response['body'],
            ];

            return false;
        }

        if (!$isCollection)
        {
            $json = $response['body'][$this->endpoint][0];

            $result = $this->createFromJson($json, static::class);
        }
    }

    protected function createFromJson($json)
    {
        foreach ($json as $name => $attribute)
        {
            if (in_array($name, array_keys($this->sharedAttrs)))
            {
                $this->attributes[$name] = $this->processAttribute($name, $attribute, $this->sharedAttrs[$name]);
            }

            if (in_array($name, array_keys($this->attrs)))
            {
                $this->attributes[$name] = $this->processAttribute($name, $attribute, $this->attrs[$name]);
            }
        }

        return $this;
    }

    protected function processAttribute($name, $attribute, $type)
    {
        if (is_array($type))
        {
            $type = $type['type'];
        }


        switch (strtolower($type))
        {
            default:

                if (in_array($name, $this->collections))
                {
                    $output = [];

                    foreach ($attribute as $one)
                    {
                        $result = $this->processChildClass($name, $one, $type);

                        if ($result)
                        {
                            $output[] = $result;
                        }
                    }

                    return collect($output);
                }

                $res = $this->processChildClass($name, $attribute, $type);

                return $res ? $res : null;

            case 'string':
                return $attribute;

            case 'guid':
                $this->guid = $attribute;
                return $this->guid;

            case 'boolean':
                return (boolean) $attribute;

            case 'float':
                return floatval($attribute);

            case 'int':
                return intval($attribute);

            case 'date':
                return new Carbon($attribute);

            case 'net-date':
                return $this->processNetDate($attribute);

            case 'array':
                return $attribute;
        }
    }

    protected function processChildClass($name, $attribute, $class)
    {

        if (!class_exists($class))
        {
            return false;
        }

        $child = new $class;

        if (!is_subclass_of($class, XeroModel::class))
        {
            throw new \Exception('The child must be a subclass of XeroModel');
        }

        return $child->createFromJson($attribute);
    }

    protected function processNetDate($netDate)
    {
        preg_match( '/\/Date\((?<ts>-?\d+)(?<tz>[+-]\d{4})\)/', $netDate, $matches);

        $time = intval($matches['ts']/1000);

        if (isset($matches['tz']))
        {
            $time += intval($matches['tz'] / 100 * 60 * 60);
        }

        return Carbon::createFromTimestamp($time);
    }

    /**
     * @return XeroModel
     */
    public static function find($guid) {
        $model = new ReflectionClass(static::class);
        $model = $model->newInstance();

        //var_dump($model);

        return $model->retrieveByGuid($guid);
    }

    public static function initFromJson($json)
    {
        //$class =  new (static::class);

        //$class->createFromJson($json);

    }

    public function save()
    {
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->toXml());
        echo $dom->saveXML();
    }

    protected function buildEndpointUrl($baseUrl)
    {
        return sprintf("%s/%s/%s/%s/", $baseUrl, $this->cats[$this->cat], $this->version, $this->endpoint);
    }

    /**
     * Get API endpoint for the current model
     *
     * @param bool $singular Should endpoint name be returned as singular
     * @return string
     */
    public function getEndpoint($singular = false)
    {
        return $singular ? str_singular($this->endpoint) : $this->endpoint;
    }

    public function getOverriddenName()
    {
        return $this->overrideName;
    }
}