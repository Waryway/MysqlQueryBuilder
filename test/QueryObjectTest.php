<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waryway\MysqlQueryBuilder\QueryObject;

//PHPUnit_TextUI_ResultPrinter

class QueryObjectTest extends TestCase
{
    public function testAdd()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new QueryObject();
        $rawQuery->BuildAdd($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->inspect()[0],
            'Expecting the exact string provided to be in the zeroeth index of the array.');

        $rawQuery->BuildAdd($queryString . ' 1st');
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->inspect()[0],
            'Expecting the exact string provided to be in the zeroeth index of the array.');
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString . ' 1st',
            $rawQuery->inspect()[1],
            'Expecting the exact string provided to be in the first index of the array.');
    }

    public function testReset()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new QueryObject();
        $rawQuery->reset();
        $this->assertEquals([], $rawQuery->inspect(),
            "Expecting an empty array after a reset of an empty expression set.");

        $rawQuery->BuildAdd($queryString);
        $rawQuery->reset();
        $this->assertEquals([], $rawQuery->inspect(),
            "Expecting an empty array after a reset of a used expression set.");
    }

    public function testToSql()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new QueryObject();
        $rawQuery->BuildAdd($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->toSql(),
            "Expecting the toSql to convert from an array to a string");
        $rawQuery->BuildAdd($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString . ' ' . '/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString,
            $rawQuery->toSql(),
            "Expecting the toSql to convert from an array to a string, by adding a space");
    }

    public function testInspect()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new QueryObject();
        $this->assertEquals([], $rawQuery->inspect(), "Expecting an empty array after initialization");

        $rawQuery->BuildAdd($queryString);
        $this->assertEquals(['/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString], $rawQuery->inspect(),
            "Expecting a loaded array");
    }

    public function testToString()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new QueryObject();
        $rawQuery->BuildAdd($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery,
            "Expecting the _toString to handle the conversion to string automagically.");
    }
}
