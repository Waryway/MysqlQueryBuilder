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

    /**
     * @param QueryObject $queryObject
     * @return bool
     */
    public function ValidateQueryObject(QueryObject $queryObject) : bool
    {
        $queryString = $queryObject->toSql();

        return $this->IsQueryStringParameterized($queryString) && $this->AreParametersValid($queryObject->getParameters(), $queryString);
    }

    /**
     * @param string $queryString
     * @return bool
     */
    private function IsQueryStringParameterized(string $queryString) : bool
    {
        $isValid = true;
        if(strstr($queryString, '?')) {
            $isValid = false;
            
            $this->errorList[] = "Invalid query string, unexpected '?', use ':' notation for parameterization. Query: ($queryString)";
        }

        if(preg_match('/(=|<|>|<=|>=|\|\||\+|\-|\*|\%|is|IS|NOT|not|in\s+\(.*|between|and|IN\s+\()\s+("|\'|[0-9])/si', $queryString)) {
            $isValid = false;
            $this->errorList[] = "Invalid query string, looks like a parameter needs to be used. Query: ($queryString)";
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
    private function AreParametersValid($params, $queryString)
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