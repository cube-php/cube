<?php

namespace App\Core\Misc;

use App\Core\Misc\Collection;

use App\Core\Misc\Input;

class Inputs extends Collection
{

    /**
     * Inputs constructor
     * 
     */
    public function __construct($content)
    {
        $parse = parse_str($content, $data);
        foreach($data as $index => $value) {
            $this->set($index, $value);
        }
    }

    /**
     * Return key
     * 
     * @param string $key Key to return
     * 
     * @return \App\Core\Misc\Input
     */
    public function get($key)
    {
        $vars = explode('.', trim($key));
        $value = $items = $this->all();

        foreach($vars as $var) {
            $value = $value[$var] ?? null;
        }

        if(!is_array($value)) return new Input($value, $var);
        return $value;
    }
}