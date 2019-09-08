# QueryBuilder
Object based query builder - because ORM's aren't always the right answer.
[![Build Status](https://travis-ci.org/Waryway/MysqlQueryBuilder.svg?branch=master)](https://travis-ci.org/Waryway/MysqlQueryBuilder)

## instructions
* do the composer thing.
* Build queries how _almost_ you normally would - just dump them into the QueryObject.

## example
The following example builds a simple query. More specific examples can be found in the [QueryObject](https://github.com/Waryway/MysqlQueryBuilder/blob/master/test/QueryObjectTest.php) test! 

    $queryString = 'select * from some_table where some_table.sky = \'blue\';';
    $rawQuery = new QueryObject();
    $rawQuery->BuildAdd($queryString);
    $rawQuery->toSql(),
