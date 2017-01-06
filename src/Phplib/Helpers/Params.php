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

use \Phplib\Helpers\HelpersException;

class Params
{
    /**
     * @var string - numbers regex patters
     */
    private $regs = array(
        'integer'   => '/^-?[0-9]+/',
        'letters'   => '/[a-zA-Z]+/',
        'string'    => '/[a-zA-Z0-9\s\#\.\,\!\?\-\_]+/',
        'double'    => '/^[0-9-]+(\\.[0-9]+)?/',
        'date'      => '/[0-9]{2,4}-[0-9]{2}-[0-9]{2}/',
        'email'     => '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD',
        'datetme'   => '/Y.m.d [0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{3}./',
        'hash'      => '/[a-zA-Z0-9\s\#\.\,\!\?\-\_\$\%\^\&\*\(\)\{\}\[\]\<\>\/\;\:]+/',
        'url'       => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'hex'       => '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/',
        'ip'        => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
        'htmltag'   => '/^<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)$/',
        'price'     => '/^\d+(,\d{1,2})?$/'
    );

    /**
     * @var string - date regex patters
     */
    const REG_DATETIME = '/Y.m.d [0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{3}./';

    /**
     * @var array $params
     */
    private $params;

    /**
     * @var array $required
     */
    private $required;

    /**
     * @var boolean $safeMode
     */
    private $safeMode;

    /**
     * Class constructor
     * @param array $params - object with GET or POST params
     * @param array $required - array od param names which are required
     * @param boolean $safeMode - safe mode
     */
    public function __construct(array $params, array $required = array(), $safeMode = true)
    {
        $this->params = $params;
        $this->required = $required;
        self::setSafeMode($safeMode);
    }

    /**
     * Method will add/update validator
     * @param string $name - name of validator
     * @param string $regCode - regular expression string
     * @return void
     */
    public function setValidator($name, $regCode)
    {
        $this->regs[$name] = $regCode;
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
     * Method will set parameter
     * @param string $name - param name to take
     * @param mixed $value - value that will be set to param
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Method will check if param exists in given GET or POST array and return value
     * @param string $name - param name to take
     * @param string $type - type of parameter to check (integer,string,text,date,datetime)
     * @param mixed $alternative - alternative value if checked param not exists or its null
     * @return mixed
     * @throws HelpersException - in case filed is required but is not valid or not exists
     */
    public function getParam($name, $type = null, $alternative = null)
    {
        if( isset($this->params[$name]) && !empty($this->params[$name]) || !empty($alternative) ) {
            $param = isset($this->params[$name]) && !empty($this->params[$name]) ? $this->params[$name] : (!empty($alternative) ? $alternative : '');
            self::applyXssProtection($param);
            $reg = self::getRegexForType($type);
            $res = !empty($reg) ? self::validate($param, $reg) ? $param : null : $param;

            if (in_array($name, $this->required) && empty($res)) {
                throw new HelpersException("PHPLIB_HELPERS_PARAMS: required param '$name' not valid.");
            } else {
                return $res;
            }
        }
        return null;
    }

    /**
     * Method will remove parameter
     * @param string $name - param name to take
     */
    public function removeParam($name)
    {
        unset($this->params[$name]);
    }

    /**
     * Method will return all params with protection if enabled
     * @return array
     */
    public function getParams()
    {
        $out = [];
        foreach($this->params as $key=>$param){
            self::applyXssProtection($param);
            $out[$key] = $param;
        }
        return $out;
    }

    /**
     * Method will check if param has valid format
     * @param string $name - param name to take
     * @param string $type - type of parameter to check (integer,string,text,date,datetime)
     * @return boolean
     */
    public function isValid($name, $type)
    {
        $param = isset($this->params[$name]) && $this->params[$name] !== '' ? $this->params[$name] : '';
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
        if ($status && $matches[0] !== '' && $matches[0] === $val) {
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
     * Method will check if given string is REDEX string
     * @param string $str -
     * @return boolean
     */
    private function isRegex($str)
    {
        $regex = "/^\/[\s\S]+\/$/";
        return (boolean)preg_match($regex, $str);
    }

    /**
     * Method will return predefined regular expression based on given type
     * @param string $type - parameter type
     * @return void
     */
    private function getRegexForType($type)
    {
        if (self::isRegex($type) && !array_key_exists($type, $this->regs)) {
            return $type;
        }

        switch ($type) {
            case 'text':
            case 'string':
                return $this->regs['string'];
                break;
            case 'letters':
                return $this->regs['letters'];
                break;
            case 'int':
            case 'integer':
                return $this->regs['integer'];
                break;
            case 'double':
                return $this->regs['double'];
                break;
            case 'datetime':
                return $this->regs['datetime'];
                break;
            case 'date':
                return $this->regs['date'];
                break;
            case 'email':
                return $this->regs['email'];
                break;
            case '':
            case null:
            default:
                return null;
                break;
        }
    }
}
