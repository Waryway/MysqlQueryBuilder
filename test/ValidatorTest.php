<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Waryway\MysqlQueryBuilder\Validator;

//PHPUnit_TextUI_ResultPrinter

class ValidatorTest extends TestCase {

    public function testIsValidQueryString() {
        $this->markTestSkipped();
    }

    public function testAreParametersValid_StandardPaths() {
        $Validator= new Validator();
        $this->assertTrue($Validator->AreParametersValid([], "someString" ), 'Expected no params + no param statement to be valid.'. print_r($Validator->getErrorList(), true));
        $this->assertTrue($Validator->AreParametersValid(['key' => 'value'], "someString :key" ), 'Expected no existing params to be valid.' . print_r($Validator->getErrorList(), true));

        $this->assertFalse($Validator->AreParametersValid(['key' => 'value'], "someString :keys" ), 'Expected no existing params to be valid.');
        $this->assertContains('Invalid Parameter (key) unable to locate it in the someString :keys', ($Validator->getErrorList()), 'Expected first entry to be an actual error');
        $this->assertCount(1, $Validator->getErrorList(), 'Expected a single error');

        $this->assertFalse($Validator->AreParametersValid(['key' => 'value'], "someString :key :key" ), 'Expected a failure as there are two key in the querystring.');
        $this->assertContains('Invalid Parameter (key) Expected 1, but found 2', ($Validator->getErrorList()), 'Expected second entry to be an actual error');
        $this->assertCount(2, $Validator->getErrorList(), 'Expected two errors.');

        $this->assertFalse($Validator->AreParametersValid(['key' => 'value'], "someString" ), 'Expected a failure as there are zero keys in the querystring.');
        $this->assertContains('Invalid Parameter (key) unable to locate it in the someString', ($Validator->getErrorList()), 'Expected a third error to appear.');
        $this->assertCount(3, $Validator->getErrorList(), 'Expected two errors.');
    }

    public function testIsQueryStringParameterized() {
        $stringToCheck = [
            [false,'select * from some_table as a where a.somcolumn = "12345"'],
            [false,'select * from some_table as a where a.somcolumn > "12345"'],
            [false,'select * from some_table as a where a.somcolumn < "12345"'],
            [false,'select * from some_table as a where a.somcolumn >= "12345"'],
            [false,'select * from some_table as a where a.somcolumn <= "12345"'],
            // fill in more operations.


        ];


        $this->markTestSkipped();
    }
}