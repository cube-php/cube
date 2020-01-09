<?php

namespace App\Providers{subNamespace};

class {className}
{
    private $_model;

    /**
     * {className} constructor
     *
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->_model = $id;
    }

    /**
     * Getter
     *
     * @param string $name Field name
     * @param array $args Arguments
     * @return mixed
     */
    public function __call($name, $args)
    {
        return $this->_model->{$name} ?? null;
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
}