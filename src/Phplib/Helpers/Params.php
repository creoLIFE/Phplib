<?php
/**
 * Helper for parameters
 * @package Phplib\Helpers
 * @author Mirek Ratman
 * @version 1.1.0
 * @since 2014-12-17
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Phplib\Helpers;

class Params
{
    /**
     * @var string - numbers regex patters
     */
    const REG_INT = '/[0-9]+/';

    /**
     * @var string - string regex patters
     */
    const REG_STRING = '/[a-zA-Z0-9\s\#\.\,\!\?\-\_]+/';

    /**
     * @var string - date regex patters
     */
    const REG_DATE = '/[0-9]{2,4}-[0-9]{2}-[0-9]{2}/';

    /**
     * @var string - date regex patters
     */
    const REG_DATETIME = '/Y.m.d [0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{3}./';

    /**
     * @var $params
     */
    private $params;

    /**
     * @var $params
     */
    private $safeMode;

    /**
     * Class constructor
     * @method __construct
     * @param string $params - object with GET or POST params
     */
    public function __construct(array $params, $safeMode = true)
    {
        $this->params = $params;
        self::setSafeMode($safeMode);
    }

    /**
     * Method will check if param exists in given GET or POST array and return value
     * @param [string] $name - param name to take
     * @param [string] $reg - regular expression to validate parameter
     * @param [mixed] $alternative - alternative value if checked param not exists or its null
     * @return string
     */
    public function setSafeMode($mode)
    {
        $this->safeMode = (boolean)$mode;
    }

    /**
     * Method will check if param exists in given GET or POST array and return value
     * @param [string] $name - param name to take
     * @param [string] $type - type of parameter to check (integer,string,text,date,datetime)
     * @param [string] $reg - regular expression to validate parameter
     * @param [mixed] $alternative - alternative value if checked param not exists or its null
     * @return string
     */
    public function getParam($name, $type = null, $reg = null, $alternative = null)
    {
        $param = isset($this->params[$name]) && !empty($this->params[$name]) ? $this->params[$name] : (!empty($alternative) ? $alternative : '');
        self::applyXssProtection($param);
        $reg = self::getRegexForType($type);
        return !empty($reg) ? self::validate($param, $reg) ? $param : null : $param;
    }

    /**
     * Method will validate given value base on delivered regular expression
     * @param [string] $val - value to check
     * @param [string] $reg - regular expression to validate value
     * @return [boolean]
     */
    private function validate($val, $reg)
    {
        $status = @preg_match($reg, $val, $matches);
        if ($status && !empty($matches[0]) && $matches[0] === $val) {
            return true;
        }
        return false;
    }

    /**
     * Method will apply safe mode
     * @param [string] $param - parameter to secure
     * @return [void]
     */
    private function applyXssProtection(&$param)
    {
        $param = $this->safeMode ? str_replace(array("&", "<", ">"), array("&amp;", "&lt;", "&gt;"), $param) : $param;
    }

    /**
     * Method will return predefined regular expression based on given type
     * @param [string] $param - parameter to check
     * @param [string] $type - parameter type
     * @return [void]
     */
    private function getRegexForType($type)
    {
        switch($type){
            case 'text':
            case 'string':
                return self::REG_STRING;
                break;
            case 'int':
            case 'integer':
                return self::REG_INT;
                break;
            case 'datetime':
                return self::REG_DATETIME;
                break;
            case 'date':
                return self::REG_DATE;
                break;
            case 'time':
                return self::REG_TIME;
                break;
            case '':
            case null:
            default:
                return null;
                break;
        }
    }

}