<?php

namespace App\Providers{subNamespace};

class {className}
{
    private $_data;

    /**
     * {className} constructor
     *
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->_data = $id;
    }

    /**
     * Getter
     *
     * @param string $name Field name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_data->{$name};
    }

    /**
     * Setter
     *
     * @param string $name Field name
     * @param mixed $value Field value
     */
    public function __set($name, $value)
    {

    }

    /**
     * Return id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}