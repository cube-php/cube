<?php

namespace App\Core\Helpers;

use Twig_Function;
use Twig_Filter;
use Twig_Environment;
use Twig_Loader_Filesystem;

use App\Core\Exceptions\ViewException;
use App\Core\Http\Env;
use App\Core\App;

class View
{
    /**
     * Views base path
     *
     * @var string
     */
    private $_base_path = '';

    /**
     * Twig
     *
     * @var Twig_Environment
     */
    private $_twig;

    /**
     * System functions
     *
     * @var array
     */
    private $_system_functions = array(
        'css',
        'jscript',
        'env',
        'url',
        'asset',
        'csrf_token',
        'csrf_form'
    );

    /**
     * View constructor
     *
     * @param string $path
     */
    public function __construct(string $path = '')
    {
        $this->setBasePath($path);
    }

    /**
     * Engage filters
     *
     * @param array $filters
     * @return void
     */
    public function engageFilters(array $filters) {
        foreach ($filters as $filter => $closure) {
            $fn = new Twig_Filter($filter, $closure, array(
                'is_safe' => array('html')
            ));

            $this->_twig->addFilter($fn);
        }
    }

    /**
     * Engage functions
     *
     * @param array $functions
     * @return void
     */
    public function engageFunctions(array $functions) {
        foreach ($functions as $function) {
            $fn = new Twig_Function($function, $function, array(
                'is_safe' => array('html')
            ));

            $this->_twig->addFunction($fn);
        }
    }

    /**
     * Set view base path
     *
     * @param string $path
     * @return void
     */
    public function setBasePath(string $path)
    {
        $loader = new Twig_Loader_Filesystem($path);
        $this->_twig = new Twig_Environment($loader, [
            'cache' => VIEW_PATH . DS . '.cache',
            'strict_variables' => App::isProduction() ? false : true
        ]);

        $this->_twig->addGlobal('env', Env::all());

        $this->engageFunctions($this->_system_functions);

        $custom_functions = App::getConfigByName('view')['functions'] ?? [];
        $custom_filters = App::getConfigByName('view')['filters'] ?? [];

        $this->engageFunctions($custom_functions);
        $this->engageFilters($custom_filters);
    }

    /**
     * Render all views
     *
     * @param string $path Path to file to render
     * @param array $datas Data to send to view
     * @return string
     */
    public function render(string $path, array $datas = [])
    {
        return $this->_twig->render(static::parsePathName($path), $datas);
    }

    /**
     * Re-parser name
     *
     * @param string $name
     * @return string
     */
    private static function parsePathName($name)
    {
        $name_vars = explode('.', $name);
        $new_name = implode('/', $name_vars);
        return $new_name . '.twig';
    }
}