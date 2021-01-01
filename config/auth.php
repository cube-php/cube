<?php

return array(

    'schema' => 'users',

    'primary_key' => 'id',

    'hash_method' => 'password_verify',

    'model' => 'App\\Models\\UsersModel',
    
    'combination' => array(
        'secret_key' => 'password',
        'fields' => array(
            'username' => null,
            //'email' => 'is_email'
        )
    )
);