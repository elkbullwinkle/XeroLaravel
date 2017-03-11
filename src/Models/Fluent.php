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

abstract class Fluent
{
    /**
     * @var XeroLaravel
     */
    protected $connection = null;

    /**
     * Retrieve model using GUID
     *
     * @param string $guid UUID of the Model
     * @return XeroModel
     */
    public static function find($guid)
    {

        $model = new ReflectionClass(static::class);
        $model = $model->newInstance();

        //var_dump($model);

        return $model->retrieveByGuid($guid);
    }

    //abstract protected function createFromJson($json);

    /**
     * Retrieve xero model by guid
     *
     * @param string $guid UUID of the Model
     * @return $this
     */
    public function retrieveByGuid($guid)
    {
        $response = $this->connection->get($guid);

        if (!$response)
        {
            return null;
        }

        return static::createFromJson(reset($response));
    }

    public static function get($paginate = false)
    {
        $model = new static;

        $url = $model->buildEndpointUrl($model->connection->baseUrl);

        $response = $model->connection->transport->get($url);

        return $model->processResponse($response, true);
    }
}