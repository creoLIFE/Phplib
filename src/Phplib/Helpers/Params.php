<?php
/**
 * Helper for parameters
 * @package Phplib\Helpers
 * @author Mirek Ratman
 * @version 1.0.0
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
     * @var $params
     */
    private $params;

    /**
     * Class constructor
     * @method __construct
     * @param string $params - object with GET or POST params
     */
    public function __construct($params = array())
    {
        $this->params = $params;
    }

    /**
     * Method will check if param exists in given GET or POST array and return value
     * @method getParam
     * @param [string] $name - param name to take
     * @param [string] $reg - regular expression to validate parameter
     * @param [mixed] $alternative - alternative value if checked param not exists or its null
     * @return string
     */
    public function getParam($name, $reg = null, $alternative = null)
    {
        $param = isset($this->params[$name]) && $this->params[$name] !== null && $this->params[$name] !== '' ? $this->params[$name] : ($alternative !== null ? $alternative : '');
        if ($reg !== null) {
            return self::validate($param, $reg) ? $param : false;
        } else {
            return $param;
        }
    }


    /**
     * Method will validate given value base on delivered regular expression
     * @method applyReg
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
}
