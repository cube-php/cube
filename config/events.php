<?php

use Cube\App\App;
use App\Events\AppCrashEvent;

return array(
    App::EVENT_APP_ON_CRASH => [
        AppCrashEvent::class
    ]
);
