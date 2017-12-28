<?php

use App\Core\Tools\Csrf;

function csrf_form()
{
    $csrf = Csrf::get();
    return '<input type="hidden" name="csrf_token" value="'. $csrf .'"/>';
}

function csrf_token()
{
    return Csrf::get();
}

function csrf($token)
{
    return Csrf::isValid($token);
}