<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Elkbullwinkle\XeroLaravel\XeroLaravel;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;

trait Retrievable {

    /**
     * XeroLaravel core reference
     *
     * @var XeroLaravel
     */
    protected $connection = null;

    /**
     * XeroLaravel configuration
     *
     * @var string
     */
    protected $config;

    /**
     * Indicates whether the model is pageable
     *
     * @var bool
     */
    protected $pageable = false;

    /**
     * Indicates whether the model is fetchable
     *
     * @var bool
     */
    protected $fetchable = true;

    /**
     * Return XeroLaravel core
     *
     * @return XeroLaravel
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set XeroLaravel core
     *
     * @param string $connection XeroLaravel configuration entry
     * @return $this
     */
    protected function setConnection(&$connection = 'default')
    {
        if ($this->connection instanceof XeroLaravel) {
            unset($this->connection);
        }

        if ($connection instanceof XeroLaravel)
        {
            $this->connection = &$connection;
        }
        else
        {
            $this->config = $connection;

            $this->connection = XeroLaravel::init($connection)
                ->setModel($this);
        }

        return $this;
    }

    /**
     * Return whether the model is pageable
     *
     * @return bool
     */
    public function isPageable()
    {
        return $this->pageable;
    }

    /**
     * Retrieve model using GUID
     *
     * @param string $guid UUID of the Model
     * @return XeroModel
     */
    public static function find($guid)
    {
        return (new static())->retrieveByGuid($guid);
    }

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

        return static::createFromJson(reset($response), $this->connection);
    }

    public function retrieveModelCollection($paginated = false, $page = 1)
    {
        $params = [];
        $headers = [];

        $query = $this->prepareQuery();

        if (!is_null($query['where'])) {
            $params['where'] = $query['where'];
        }

        if (!is_null($query['order'])) {
            $params['order'] = $query['order'];
        }

        if (!is_null($query['modified'])) {
            $headers['If-Modified-Since'] = $query['modified'];
        }

        if ($paginated) {
            $params['page'] = $page;
        }

        //dd($query, 'Getting model collection');

        $response = $this->connection->get(null, $params, $headers);

        if (!$response)
        {
            return collect();
        }

        $collection = static::createCollectionFromJson($response, $this->connection)
            ->setPage($page)
            ->setPaginated($paginated);

        return $collection;
    }

    protected function prepareQuery()
    {
        $where = (string)$this->getBuilderInstance();
        $order = $this->getBuilderInstance()->compileOrderBy();
        $modified = $this->getBuilderInstance()->compileModifiedAfter();

        return [
            'where' => !is_null($where) ? $where : null,
            'order' => !is_null($order) ? $order : null,
            'modified' => !is_null($modified) ? $modified : null,
        ];
    }

}