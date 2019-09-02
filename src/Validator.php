<?php
namespace Waryway\MysqlQueryBuilder;

use Waryway\PhpLogger;

class Validator
{
    private $errorList = [];

    /**
     * @return array
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    public function ValidateQueryObject(QueryObject $queryObject)
    {
        $queryString = $queryObject->toSql();

        $this->IsQueryStringParameterized($queryString);
        $this->AreParametersValid($queryObject->getParameters(), $queryString);

    }

    public function IsQueryStringParameterized(string $queryString) : bool
    {
        $isValid = true;
        if(strstr($queryString, '?')) {
            $isValid = false;
            
            $this->errorList[] = "Invalid query string, unexpected '?', use ':' notation for parameterization. Query: ($queryString)";
        }
        
        
        return $isValid;
    }

    /**
     * Checks for all the parameter errors it can find in the current query.
     *
     * @param $params
     * @param $queryString
     * @return bool
     */
    public function AreParametersValid($params, $queryString)
    {
        $isValid = true;
        foreach ($params as $key => $param) {
            $found = 0;
            if(preg_match('/:' . $key . '( |,|$)/', $queryString)) {
                $queryString = preg_replace('/:' . $key . '/', $param . ' ', $queryString, -1, $found);
                if ($found != 1) {
                    $this->errorList[] = "Invalid Parameter ($key) Expected 1, but found $found";
                    $isValid = false;
                }
            } else {
                $this->errorList[] = "Invalid Parameter ($key) unable to locate it in the $queryString";
                $isValid = false;
            }
        }

        return $isValid;
    }
}