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
    private static $_custom_validators = [];

    /**
     * Validation errors
     * 
     * @var array
     */
    private static $_validation_errors = [];

    /**
     * Validation error messages
     *
     * @var array
     */
    private static $_validation_errors_msgs = [];

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
        'required' => '{input} is required',
        'equals' => '{input} must be equal to {value}'
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
     * Callung anonymous methods
     *
     * @param string $name Method name
     * @param string $args Arguments
     * @return self
     */
    public function __call($name, $args)
    {

        $validators = static::$_custom_validators;

        if(!array_key_exists($name, $validators)) {
            throw new InvalidArgumentException('Validator "'. $name .'" not found');
        }

        $method_name = $validators[$name];
        $args = array_merge($validator = [$this], $args);

        return call_user_func_array($method_name, $args);
    }

    /**
     * Extend validator
     * 
     * @param array $methods Methods to extend the validator
     * 
     * @return void
     */
    public static function extend($methods)
    {
       foreach($methods as $name => $method)
       {
           static::$_custom_validators[$name] = $method;
       }
    }

    /**
     * Return error at specified index
     *
     * @param int $index
     * @return string
     */
    public static function getErrorByIndex($index)
    {
        return static::$_validation_errors_msgs[$index] ?? null;
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
     * Return the first error
     *
     * @return void
     */
    public static function getFirstError()
    {
        return static::getErrorByIndex(0);
    }

    /**
     * Starts/refreshes input validator
     * 
     * @param array $extensions Callback functions to extend validator
     * 
     * @return void
     */
    public static function init($extensions = [])
    {
        static::$_validation_errors_msgs = static::$_validation_errors = [];

        #If there are any extensions call the extend method
        #And pass in the extensions
        if($extensions) {
            static::extend($extensions);
        }
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
     * Get error message
     *
     * @param string $field Field name
     * @param string $msg Custom message
     * @return string
     */
    private static function getMessage($field, $msg)
    {
        return $msg ? $msg : static::$_messages[$field];
    }
    
    /**
     * Check if input's value is equal to value
     *
     * @param string|int $value
     * @param string|null $msg Custom error message
     * 
     * @return self
     */
    public function equals($value, $msg = null)
    {
        $msg = static::getMessage('equals', $msg);

        if($value != $this->getValue()) {
            $this->attachError($msg, ['value' => $value]);
        }

        return $this;
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
        $msg = static::getMessage('email', $msg);

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

        $msg = static::getMessage('max_length', $msg);

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
        $msg = static::getMessage('min_length', $msg);

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
        $msg = static::getMessage('required', $msg);

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
        $msg = static::getMessage('url', $msg);

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

        static::$_validation_errors_msgs[] = $error;
        static::$_validation_errors[$this->_id][] = $error;

        return $this;
    }
}