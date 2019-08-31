<?php
namespace Waryway\MysqlQueryBuilder\Raw;

use Waryway\PhpLogger;

class Query
{
    /**
     * @var array - Keep track of the strings used to build the raw expression.
     */
    private $rawExpression = [];

    /**
     * Adds to an existing RawQueryExpression.
     *
     * @param $queryText
     * @return $this
     */
    public function Add($queryText)
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        
        $this->rawExpression[] = '/*' . $caller['class'] . ':' . $caller['function'] . '*/' . $queryText;
        return $this;
    }

    /**
     * Resets the expression to empty.
     *
     * @return $this
     */
    public function reset()
    {
        $this->rawExpression = [];
        return $this;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return implode($this->rawExpression, ' ');
    }

    /**
     * Returns the raw expression.
     * @return array
     */
    public function inspect()
    {
        return $this->rawExpression;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->toSql();
    }
}