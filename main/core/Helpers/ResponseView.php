<?php

namespace App\Core\Helpers;

use Dwoo\Core;
use App\Core\Exceptions\ResponseViewException;

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
        $raw_file_vars = explode('.', $file);
        $raw_filename = implode('/', $raw_file_vars);

        $filename = VIEW_PATH . DS . $raw_filename . $this->extension;
        if(!file_exists($filename)) {
            throw new ResponseViewException($file . '.tpl not found in ' . VIEW_PATH);
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
        foreach($items as $field => $value) {
            static::assign($field, $value);
        }
    }
}