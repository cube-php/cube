<?php

return array(

    'schema' => 'schema_name',

    'primary_key' => 'primary_key',

    'hash_method' => 'password_verify',

    'instance' => 'App\\Providers\\AccountsProvider',
    
    'combination' => array(
        array(
            'secret_key' => 'password',
            'fields' => ['username', 'email']
        )
    )
);