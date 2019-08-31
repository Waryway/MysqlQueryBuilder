<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
//use 
//PHPUnit_TextUI_ResultPrinter
use Waryway\MysqlQueryBuilder\Select\Column;

class ColumnTest extends TestCase
{
    public function testCol() {
        $col = new Column();
        $col->Col('some_column', 'alias');
        print_r($col);
    }
}
