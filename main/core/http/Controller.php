<?php

namespace App\Core\Http;

use App\Core\Http\Request;

use App\Core\Helpers\ResponseView;

class Controller
{

    /**
     * Controller constructor
     * 
     */
    public function __construct()
    {

        #Assign url
        $request = new Request;
        $url = $request->url();

        #Assign required variables to view
        ResponseView::multiAssign(
            array(
                'url' => $url->getUrl(),
                'current_url' => $url->getFullUrl()
            )
        );
    }
}