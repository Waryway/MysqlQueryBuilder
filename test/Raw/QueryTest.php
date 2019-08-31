<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waryway\MysqlQueryBuilder\Raw\Query;

//PHPUnit_TextUI_ResultPrinter

class RawTest extends TestCase
{
    public function testAdd()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new Query();
        $rawQuery->Add($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->inspect()[0],
            'Expecting the exact string provided to be in the zeroeth index of the array.');

        $rawQuery->Add($queryString . ' 1st');
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->inspect()[0],
            'Expecting the exact string provided to be in the zeroeth index of the array.');
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString . ' 1st',
            $rawQuery->inspect()[1],
            'Expecting the exact string provided to be in the first index of the array.');
    }

    public function testReset()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new Query();
        $rawQuery->reset();
        $this->assertEquals([], $rawQuery->inspect(),
            "Expecting an empty array after a reset of an empty expression set.");

        $rawQuery->Add($queryString);
        $rawQuery->reset();
        $this->assertEquals([], $rawQuery->inspect(),
            "Expecting an empty array after a reset of a used expression set.");
    }

    public function testToSql()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new Query();
        $rawQuery->Add($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery->toSql(),
            "Expecting the toSql to convert from an array to a string");
        $rawQuery->Add($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString . ' ' . '/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString,
            $rawQuery->toSql(),
            "Expecting the toSql to convert from an array to a string, by adding a space");
    }

    public function testInspect()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new Query();
        $this->assertEquals([], $rawQuery->inspect(), "Expecting an empty array after initialization");

        $rawQuery->Add($queryString);
        $this->assertEquals(['/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString], $rawQuery->inspect(),
            "Expecting a loaded array");
    }

    public function testToString()
    {
        $queryString = 'select * from some_table where some_table.sky = \'blue\';';
        $rawQuery = new Query();
        $rawQuery->Add($queryString);
        $this->assertEquals('/*' . __CLASS__ . ':' . __FUNCTION__ . '*/' . $queryString, $rawQuery,
            "Expecting the _toString to handle the conversion to string automagically.");
    }
}
