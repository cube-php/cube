<?php

use App\Middlewares\Authentication;
use App\Middlewares\CsrfMiddleware;

return array(
    'csrf' => CsrfMiddleware::class
);
