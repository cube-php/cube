<?php

namespace App\Core\Tools;

use InvalidArgumentException;

use App\Core\App;

use App\Core\Modules\DB;

use App\Core\Http\Request;

use App\Core\Http\Response;

use App\Core\Http\Session;

use App\Core\Interfaces\MiddlewareInterface;

use App\Core\Exceptions\AuthException;

class Auth implements MiddlewareInterface
{

    /**
     * Authentication configuration
     * 
     * @param string[]
     */
    private static $_config;

    /**
     * Get authentication status
     * 
     * @return string[]
     */
    private static $_auth_name = 'session_auth';
    

    /**
     * Attempt authentication
     * 
     * @param array $combination
     * @param boolean $remember
     * 
     * @return object|boolean
     */
    public static function attempt($combination, $remember = false)
    {
        #Load the auth configuaration
        $config = static::getConfig();

        $hash_method = $config['hash_method'] ?? 'password_verify';
        $primary_key = $config['primary_key'] ?? null;
        $schema = $config['schema'] ?? null;
        $config_combination = $config['combination'];

        if(!$schema) {
            throw new AuthException('Auth schema field is undefined');
        }

        #Get the number of assigned fields
        $args_count = count($combination);

        if($args_count !== 2) {
            throw new InvalidArgumentException
                ('Auth::attempt() should contain an array of two fields, the ID and Password only, ' . $args_count . ' found');
        }

        $keys = array_keys($combination);
        $secret_key_field = 'secret_key';

        $specified_key = $keys[0];
        $specified_secret_key = $combination['secret_key'] ?? '';
        $specified_field_value = $combination[$specified_key];

        if(!isset($combination['secret_key'])) {
            throw new AuthException('Secret Key field is not specified');
        }

        #Check if the secret key field exist,
        #Throw an AuthException if an error occured
        if(!array_key_exists($secret_key_field, $combination)) {
            throw new AuthException('No secret key field found');
        }

        #Get the value of secret key and remove it from field
        $secret_key = $combination[$secret_key_field];
        unset($combination[$secret_key_field]);
        
        $selected_model = null;

        foreach($config_combination as $combo) {
            if(in_array($specified_key, $combo['fields'])) {
                $selected_model = $combo;
                break;
            }
        }

        if(!$selected_model) {
            throw new AuthException('Authentication model "'. $specified_key .'" not found');
        }

        $secret_key_model_name = $selected_model['secret_key'];

        $query = DB::table($schema)
                    ->select([$primary_key, $secret_key_model_name])
                    ->where($specified_key, $specified_field_value)
                    ->fetchOne();

        if(!$query) {
            throw new AuthException('Account not found for ' . $specified_field_value);
        }

        $raw_server_secret = $query->{$secret_key_model_name};
        $secret_is_valid = $hash_method($secret_key, $raw_server_secret);

        if(!$secret_is_valid) {
            throw new AuthException('Invalid account credentials');
        }

        Session::set(static::$_auth_name, $query->{$primary_key});
        return true;
    }

    /**
     * Handle middleware
     * 
     * @param \App\Core\Http\Request $request
     * @param \App\Core\Http\Response $response
     * 
     * @return \App\Core\Http\Response
     */
    public function handle(Request $request, Response $response)
    {
        Session::get(static::$_auth_name);
        $request->addMiddleWare('auth', null);
    }

    /**
     * Get the current authenticated user
     * 
     * @return object|boolean
     */
    public static function user()
    {
        $auth_id = Session::get(static::$_auth_name);

        if(!$auth_id) {
            return false;
        }

        $instance = static::getConfig()['instance'];
        return new $instance($auth_id);
    }

    /**
     * Get Auth config
     * 
     * @return string[]
     */
    private static function getConfig()
    {
        if(!static::$_config) {
            static::$_config = App::getConfigByName('auth');
        }

        if(!static::$_config) {
            throw new AuthException('Auth config not found in "' . CONFIG_PATH . '"');
        }

        return static::$_config;
    }
}