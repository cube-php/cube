<?php

return array(

    'schema' => 'users',

    'primary_key' => 'uid',

    'hash_method' => 'password_verify',

    'instance' => 'App\\Providers\\UserProvider',
    
    'combination' => array(
        array(
            'secret_key' => 'password',
            'fields' => ['username', 'email']
        )
    )
);