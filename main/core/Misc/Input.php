<?php

namespace App\Core\Misc;

use InvalidArgumentException;

use App\Core\Interfaces\InputInterface;

use App\Core\Misc\InputValidator;

class Input implements InputInterface
{

    /**
     * Key holder
     * 
     * @var mixed
     */
    private $key = '';

    /**
     * Value holder
     * 
     * @var mixed
     */
    private $value = '';

    /**
     * Input constructor
     * 
     * @param string $value
     * @param string $key
     */
    public function __construct($value, $key = '')
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Return $this->value when treated as string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->value();
    }

    /**
     * Check if input's value matches specified value
     *
     * @param mixed $value
     * @return boolean
     */
    public function equals($value)
    {
        return $this->value() == $value;
    }

    /**
     * Check if $value is email
     * 
     * @return bool
     */
    public function isEmail()
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if $value is int
     * 
     * @return bool
     */
    public function isInt()
    {
        return (is_numeric($this->value) || is_int($this->value));
    }

    /**
     * Check if $value is regular expression
     * 
     * @return bool
     */
    public function isRegex()
    {
        return filter_var($this->value, FILTER_VALIDATE_REGEXP);
    }

    /**
     * Check if $value is empty
     * 
     * @return bool
     */
    public function isEmpty()
    {
        return empty(trim($this->value));
    }

    /**
     * Check if $value is URL
     * 
     * @return bool
     */
    public function isUrl()
    {
        return filter_var($this->value, FILTER_VALIDATE_URL);
    }

    /**
     * Check if $value matches regex
     * 
     * @param string $regex Regular expression
     * 
     * @return bool
     */
    public function matches($regex)
    {
        return !!preg_match($regex, $this->value);
    }

    /**
     * Create input's validator instance
     * 
     * @return \App\Core\Misc\InputValidator
     */
    public function validate()
    {

        if(!$this->key) {
            throw new InvalidArgumentException
                ('You are required to specify a key for input to use validator');
        }

        return new InputValidator($this->key, $this->value);
    }

    /**
     * Return input's value as an integer
     *
     * @return int
     */
    public function toInt()
    {
        return (int) $this->value();
    }

    /**
     * Return input's value
     * 
     * @return string
     */
    public function value()
    {
        return $this->value;
    }
}