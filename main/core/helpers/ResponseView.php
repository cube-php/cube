<?php

namespace App\Core\Helpers;

use Dwoo\Core;

class ResponseView
{

    /**
     * File extension
     * 
     * @var string
     */
    private $extension = '.tpl';

    /**
     * Template path
     * 
     * @var string
     */
    private $path;

    /**
     * Dwoo
     * 
     * @var \Dwoo\Core
     */
    private $dwoo;

    /**
     * Data
     * 
     * @var array
     */
    static $data = [];

    /**
     * Constructor
     * 
     * @param string $file Filename
     */
    public function __construct($file)
    {

        $filename = VIEW_PATH . DS . $file . $this->extension;
        if(!file_exists($filename)) {
            throw new ResponseViewException($file . '.mustache not found in ' . VIEW_PATH);
        }

        $this->path = $filename;

        $this->dwoo = new Core;
    }

    /**
     * Render view content
     * 
     * @param array $params Binding parameters
     * 
     * @return string
     */
    public function renderViewContent(array $params = [])
    {
        $this->multiAssign($params);
        return $this->dwoo->get($this->path, static::$data);
    }

    /**
     * assign new value
     * 
     * @param string $field Field name
     * @param string $value Value
     * 
     * @return
     */
    public static function assign($field, $value)
    {
        static::$data[$field] = $value;
    }

    /**
     * Multi assign
     * 
     * @param array $data
     * 
     * @return void
     */
    public static function multiAssign($items)
    {
        foreach($items as $field => $value)
        {
            static::assign($field, $value);
        }
    }
}