<?php
namespace Waryway\MysqlQueryBuilder\Select;

use Waryway\MysqlQueryBuilder\SelectAbstract;
use Waryway\PhpLogger\FileLog;

class Column extends SelectAbstract {

    /**
     * @var array
     */
    private $stack = [];

    /**
     * @param $item
     */
    private function addToStack(string $item) {
        echo 'Adding ' . $item . PHP_EOL;
        $this->stack[] = $item;
    }

    public function Col($column, $alias, $withParens = false) {
        $this->addToStack(($withParens ? '(' : '') . $column . (is_null($alias) ? '' : ' as ' . $alias) . ($withParens ? ')' : '') );
        return $this;
    }

    public function SubSelect($select, $alias = null) {

        return $this;
    }

    public function __toString()
    {
        return implode($this->stack, ', ');
    }
}
