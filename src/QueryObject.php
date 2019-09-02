<?php
namespace Waryway\MysqlQueryBuilder;

use Waryway\PhpLogger;

class QueryObject
{
    /**
     * @var $parameters array
     */
    private $parameters = [];

    /**
     * @var $queryObjects QueryObject[]
     */
    private $queryObjects = [];

    /**
     * @var array - Keep track of the strings used to build the raw expression.
     */
    private $rawExpression = [];

    /**
     * @var Validator
     */
    private $Validator = null;

    /**
     * @param $key
     * @param $value
     * @return QueryObject
     * @throws \InvalidArgumentException - if the parameter is already used as a query object.
     */
    public function setParameter($key, $value) : QueryObject
    {
        if (!key_exists($key, $this->queryObjects)) {
            $this->parameters[$key] = $value;
        } else {
            throw new \InvalidArgumentException('Unable to set Parameter, Key: ' . $key . ' already in use.');
        }

        return $this;
    }

    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Validator
     */
    public function getValidator() : Validator
    {
        return $this->Validator ?? new Validator();
    }

    /**
     * @param Validator $Validator
     */
    public function setValidator(Validator $Validator) : QueryObject
    {
        $this->Validator = $Validator;
        return $this;
    }

    /**
     * @param $queryText
     * @param array $params
     * @param QueryObject[] ...$queryObjects
     * @return array
     */
    public function BuildAdd($queryText, $params = [], $queryObjects = []) : QueryObject
    {   
        foreach($queryObjects as $queryObject) {
            $params = array_merge($params, $queryObject->getParameters());
        }
        
        $this->parameters = $params;
        $this->queryObjects = $queryObjects;
        
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

        $this->rawExpression[] = '/*' . $caller['class'] . ':' . $caller['function'] . '*/' . $queryText;
        return $this;
    }

    /**
     * Resets the expression to empty.
     *
     * @return $this
     */
    public function reset() : QueryObject
    {
        $this->rawExpression = [];
        return $this;
    }

    /**
     * @return string
     */
    public function toSql() : string
    {
        $queryString = implode($this->rawExpression, ' ');
        foreach ($this->queryObjects as $key => $queryObject) {
            preg_replace('/:' . $key . '/', $queryObject->toSql(), $queryString, 1);
        }

        return $queryString;
    }

    /**
     * Returns the raw expression.
     * @return array
     */
    public function inspect() : array
    {
        return $this->rawExpression;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {

        return $this->toSql();
    }

}