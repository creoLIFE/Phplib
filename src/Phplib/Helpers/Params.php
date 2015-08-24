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
     * @var string - letters regex patters
     */
    const REG_LETTERS = '/[a-zA-Z]+/';

    /**
     * @var string - string regex patters
     */
    const REG_STRING = '/[a-zA-Z0-9\s\#\.\,\!\?\-\_]+/';

    /**
     * @var string - date regex patters
     */
    const REG_DATE = '/[0-9]{2,4}-[0-9]{2}-[0-9]{2}/';

    /**
     * @var string
     */
    const REG_EMAIL = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

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
     * @param array $params - object with GET or POST params
     * @param boolean $safeMode - safe mode
     */
    public function __construct(array $params, $safeMode = true)
    {
        $this->params = $params;
        self::setSafeMode($safeMode);
    }

    /**
     * Method will set safe mode
     * @param string $mode - param name to take
     * @return string
     */
    public function setSafeMode($mode)
    {
        $this->safeMode = (boolean)$mode;
    }

    /**
     * Method will check if param exists in given GET or POST array and return value
     * @param string $name - param name to take
     * @param string $type - type of parameter to check (integer,string,text,date,datetime)
     * @param mixed $alternative - alternative value if checked param not exists or its null
     * @return string
     */
    public function getParam($name, $type = null, $alternative = null)
    {
        $param = isset($this->params[$name]) && !empty($this->params[$name]) ? $this->params[$name] : (!empty($alternative) ? $alternative : '');
        self::applyXssProtection($param);
        $reg = self::getRegexForType($type);
        return !empty($reg) ? self::validate($param, $reg) ? $param : null : $param;
    }

    /**
     * Method will check if param has valid format
     * @param string $name - param name to take
     * @param string $type - type of parameter to check (integer,string,text,date,datetime)
     * @return boolean
     */
    public function isValid($name, $type)
    {
        $param = isset($this->params[$name]) && !empty($this->params[$name]) ? $this->params[$name] : '';
        self::applyXssProtection($param);
        $reg = self::getRegexForType($type);
        return !empty($reg) ? self::validate($param, $reg) ? true : false : false;
    }

    /**
     * Method will validate given value base on delivered regular expression
     * @param string $val - value to check
     * @param string $reg - regular expression to validate value
     * @return boolean
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
     * @param string $param - parameter to secure
     * @return void
     */
    private function applyXssProtection(&$param)
    {
        $param = $this->safeMode ? str_replace(array("&", "<", ">"), array("&amp;", "&lt;", "&gt;"), $param) : $param;
    }

    /**
     * Method will return predefined regular expression based on given type
     * @param string $type - parameter type
     * @return void
     */
    private function getRegexForType($type)
    {
        switch ($type) {
            case 'text':
            case 'string':
                return self::REG_STRING;
                break;
            case 'letters':
                return self::REG_LETTERS;
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
            case 'email':
                return self::REG_EMAIL;
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