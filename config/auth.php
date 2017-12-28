<?php

return array(

    'schema' => 'account',

    'primary_key' => 'id',

    'hash_method' => 'password_verify',

    'instance' => 'App\\Providers\\DemoProvider',
    
    'combination' => array(
        array(
            'secret_key' => 'password',
            'fields' => ['username', 'email']
        )
    )
);