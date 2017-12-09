<?php

namespace App\Core\Misc;

use Closure;

use InvalidArgumentException;

class InputValidator
{
    
    /**
     * Input identifier
     * 
     * @param string
     */
    private $_id;

    /**
     * Input
     * 
     * @var mixed
     */
    private $_input;

    /**
     * Custom validation methods
     * 
     * @param
     */
    public static $_custom_validators = [];

    /**
     * Validation errors
     * 
     * @var array
     */
    private static $_validation_errors;

    /**
     * Validator messages
     * 
     * @var array
     */
    private static $_messages = array(
        'max_length' => '{input} should not exceed a maximum {length} chars',
        'min_length' => '{input} should be a minimum of {length}',
        'email' => '{input} is required to be a valid email address',
        'url' => '{input} is required to be a valid url',
        'number' => '{input} is required to be a number',
        'required' => '{input} is required'
    );

    /**
     * Constructor
     * 
     * @param string $id Input identifier
     * @param mixed $input
     */
    public function __construct($id, $input)
    {
        $this->_input = $input;
        $this->_id = $id;
    }

    /**
     * 
     */
    public function __call($name, $args)
    {

        $validators = static::$_custom_validators;

        if(!array_key_exists($name, $validators)) {
            throw new InvalidArgumentException('Validator "'. $name .'" not found');
        }

        $method_name = $validators[$name];
        return $method_name($this, $args);
    }

    /**
     * Extend validator
     * 
     * @param string $method_name Validator name
     * 
     * @return
     */
    public static function extend($methods)
    {
       foreach($methods as $name => $method)
       {
           static::$_custom_validators[$name] = $method;
       }
    }

    /**
     * Return validator errors
     * 
     * @return array
     */
    public static function getErrors()
    {
        return static::$_validation_errors;
    }

    /**
     * Check if validations are valid
     * 
     * @return bool
     */
    public static function isValid()
    {
        $errors = self::getErrors();
        return (count($errors) == 0);
    }

    /**
     * Email validator
     * 
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function email($msg = null)
    {
        $msg = $msg ?? static::$_messages['email'];

        if(!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
            $this->attachError($msg);
        }

        return $this;
    }

    /**
     * Return validator id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Return validator value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->_input;
    }
    
    /**
     * Maximum length validator
     * 
     * @param int $length
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function maxLength($length, $msg = null)
    {

        $msg = $msg ?? static::$_messages['max_length'];

        if(strlen($this->getValue()) > $length) {
            $this->attachError($msg, ['length' => $length]);
        }

        return $this;
    }

    /**
     * Minimum length validator
     * 
     * @param int $length
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function minLength($length, $msg = null)
    {
        $msg = $msg ?? static::$_messages['min_length'];

        if(strlen($this->getValue()) < $length) {
            $this->attachError($msg, ['length' => $length]);
        }

        return $this;
    }

    /**
     * Is number validator
     * 
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function number($msg = null)
    {
        $msg = $msg ?? static::$_messages['number'];

        if(!is_numeric($this->getValue())) {
            $this->attachError($msg, ['length']);
        }

        return $this;
    }

    /**
     * Required validator
     * 
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function required($msg = null)
    {
        $msg = $msg ?? static::$_messages['required'];

        if(!$this->getValue()) {
            $this->attachError($msg);
        }

        return $this;
    }

    /**
     * Url validator
     * 
     * @param string $msg Custom error message
     * 
     * @return self
     */
    public function url($msg = null)
    {
        $msg = $msg ?? static::$_messages['url'];

        if(!filter_var($this->getValue(), FILTER_VALIDATE_URL)) {
            $this->attachError($msg);
        }

        return $this;
    }

    /**
     * Attach new validation error
     * 
     * @return self
     */
    public function attachError($error, $vars = [])
    {
       $vars['input'] = $this->_id;

        foreach($vars as $key => $value)
        {
            $replacer = '{'. $key .'}';
            $error = str_replace($replacer, $value, $error);
        }

        static::$_validation_errors[$this->_id][] = $error;
    }
}