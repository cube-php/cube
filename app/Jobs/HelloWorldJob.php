<?php

namespace App\Jobs;

use Cube\Interfaces\JobsInterface;

class HelloWorldJob implements JobsInterface
{
    public function handle()
    {
        //handle job
    }
}