<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waryway\MysqlQueryBuilder\Validator;
use Waryway\MysqlQueryBuilder\QueryObject;

//PHPUnit_TextUI_ResultPrinter

class ValidatorTest extends TestCase {

    /**
     * @var Validator
     */
    private static $Validator;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$Validator = new Validator();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $errorList = self::$Validator->getErrorList();
        print_r($errorList);
        $expectedErrors = [
            'Invalid Parameter (key) unable to locate it in the /*ValidatorTest:testIsValidQueryString*/select someString from some_table where key = :keys',
            'Invalid Parameter (key) Expected 1, but found 2',
            'Invalid Parameter (key) unable to locate it in the /*ValidatorTest:testIsValidQueryString*/select someString from some_table',
            'Invalid query string, unexpected \'?\', use \':\' notation for parameterization. Query: (/*ValidatorTest:testIsValidQueryString*/select * from table where column = ?)',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn > "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn < "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn >= "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn <= "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where ' . PHP_EOL . 'a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where ' . "\r\n" . 'a.somcolumn = "12345")',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn between 12345 and 1987)',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn between :someparams and 1987)',
            'Invalid query string, looks like a parameter needs to be used. Query: (/*ValidatorTest:testIsValidQueryString*/select * from some_table as a where a.somcolumn in (:someparams, :1987, \'1987\'))'
        ];
        foreach($expectedErrors as $expectedError) {
            self::assertContains($expectedError, $errorList, "Expected this specific error to appear");
        }
        self::assertCount(count($expectedErrors), $errorList, "Making sure the total number of errors is correct");
    }

    /**
     * @dataProvider ParameterizedQueryStringProvider
     */
    public function testIsValidQueryString($expected, $string, $params = [], $message = 'Something unexpected happened, no message provided.'): void {

        $queryObject = new QueryObject();
        $queryObject->setValidator(self::$Validator);
        $queryObject->BuildAdd($string, $params, []);
        $actual = self::$Validator->ValidateQueryObject($queryObject);
        $this->assertEquals($expected, $actual, $message);
    }

    public function ParameterizedQueryStringProvider(): array {
        return [
            [true, 'select * from table where column = :someColumn', [], "Looks like a valid query to me."],
            [true, 'select someString from some_table where key = ', [], 'Expected no params + no param statement to be valid.'],
            [true, 'select someString from some_table where key = :key', ['key' => 'value'], 'Expected no missing params to be valid.'],
            [false, 'select someString from some_table where key = :keys', ['key' => 'value'], 'Invalid Parameter (key) unable to locate it in the someString :keys'],
            [false, 'select someString from some_table where key = :key :key', ['key' => 'value'], 'Invalid Parameter (key) Expected 1, but found 2'],
            [false, 'select someString from some_table',['key' => 'value'], 'Invalid Parameter (key) unable to locate it in the someString'],
            [false, 'select * from table where column = ?', [], 'Parameterized notation must be :arg instead of ?'],
            [false,'select * from some_table as a where a.somcolumn = "12345"', [], '= Operation followed by a string is invalid'],
            [false,'select * from some_table as a where a.somcolumn > "12345"', [], '> Operation followed by a string is invalid'],
            [false,'select * from some_table as a where a.somcolumn < "12345"', [], '< Operation followed by a string is invalid'],
            [false,'select * from some_table as a where a.somcolumn >= "12345"', [], '>= Operation followed by a string is invalid'],
            [false,'select * from some_table as a where a.somcolumn <= "12345"', [], '<= Operation followed by a string is invalid'],
            [false,'select * from some_table as a where '.PHP_EOL.'a.somcolumn = "12345"', [], 'multi line query, Operation followed by a string is invalid'],
            [false,'select * from some_table as a where '."\r\n".'a.somcolumn = "12345"', [], 'multi line query,  Operation followed by a string is invalid'],
            [false,'select * from some_table as a where '."\n".'a.somcolumn = "12345"', [], 'multi line query, Operation followed by a string is invalid'],
            [false,'select * from some_table as a where '. "\r" .'a.somcolumn = "12345"', [], 'multi line query, Operation followed by a string is invalid'],
            [false,'select * from some_table as a where '. "\r" .'a.somcolumn = 12345', [], 'multi line query, Operation followed by a number is invalid'],
            [false,'select * from some_table as a where a.somcolumn between 12345 and 1987', [], 'between Operation followed by a number is invalid'],
            [false,'select * from some_table as a where a.somcolumn between :someparams and 1987', [], 'between and, Operation followed by a number is invalid'],
            [false,'select * from some_table as a where a.somcolumn in (:someparams, :1987, \'1987\')', [], 'in, Operation followed by a number is invalid'],
        ];
    }
}