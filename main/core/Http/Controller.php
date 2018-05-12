<?php

namespace App\Core\Http;

use App\Core\Http\Request;
use App\Core\Helpers\ResponseView;

class Controller
{

    /**
     * Instance of self
     *
     * @var Controller;
     */
    private static $_instance;

    /**
     * Controller constructor
     * 
     */
    public function __construct()
    {
        static::__init__(new Request);
    }

    /**
     * Get controller's instance
     *
     * @return Controller
     */
    public static function getInstance()
    {
        if(!static::$_instance) {
            static::$_instance = new self;
        }
        
        return static::$_instance;
    }

    /**
     * Initialize the controller to assign default attributes
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    private static function __init__(Request $request)
    {
        $url = $request->url();

        #Assign required variables to view
        ResponseView::multiAssign(
            array(
                '_url' => $url->getUrl(),
                '_current_url' => $url->getFullUrl(),
                '_current_path' => $url->getPath(),
                '_request_method' => $request->getMethod()
            )
        );
    }
}