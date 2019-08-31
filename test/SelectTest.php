<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
//use 
//PHPUnit_TextUI_ResultPrinter
use Waryway\MysqlQueryBuilder\Select;

class SelectTest extends TestCase
{
    public function testCol() {
        $query = (new Select())->All();
        print_r($query->toSql());
        
        $query = (new Select())->Column('ColumnNameOne', 'Coolcolumn')->Column('ColumnNameTwo', 'Boring')->Column((new Select())->All(), 'subquery');
        print_r($query->toSql());

    }
}
