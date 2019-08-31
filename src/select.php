<?php
namespace Waryway\MysqlQueryBuilder;

use Waryway\PhpLogger;
use Waryway\MysqlQueryBuilder\Select\Column;

class Select extends SelectAbstract {

    private $selectExpression = [];

    private $col = null;

    public function __construct()
    {
        $this->col = new Column();
    }

    public function All() {
        $this->Column('*');
        return $this;
    }

    public function Column ($name, $alias = null) {
        $this->col->Col($name, $alias, ($name instanceof Select));
        return $this;
    }

    public function toSql() {
        $result = '';
        foreach($this->selectExpression as $expression => $value) {
            echo $expression .' ' . $value . PHP_EOL;
            $result .= (string)$expression . ', ';
        }
        $result .= $this->col;

        return 'select ' . $result;
    }

    public function __toString()
    {
        return $this->toSql();
    }

}