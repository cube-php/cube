<?php

namespace App\Core\Tools;

use InvalidArgumentException;

use App\Core\App;
use App\Core\Modules\DB;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Session;
use App\Core\Http\Cookie;
use App\Core\Exceptions\AuthException;

class Auth
{

    /**
     * Authentication configuration
     * 
     * @var string[]
     */
    private static $_config;

    /**
     * Get authentication status
     * 
     * @var string[]
     */
    private static $_auth_name = 'session_auth';

    /**
     * Cube cookie token dbname
     *
     * @var string
     */
    private static $_cookie_token_dbname = 'cube_auth_tokens';

    /**
     * Get authenticated user
     * 
     * @var object
     */
    private static $_auth_user;
    

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
        #Check schema
        static::up();

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

        $schema_primary_key = $query->{$primary_key};

        #Check for the remeber feature
        if($remember) {
            static::setUserCookieToken($schema_primary_key);
        }

        Session::set(static::$_auth_name, $schema_primary_key);
        return true;
    }

    /**
     * End current authenticated user's session
     *
     * @return void
     */
    public static function logout()
    {
        Session::remove(static::$_auth_name);
        Cookie::remove(static::$_auth_name);
    }

    /**
     * Get the current authenticated user
     * 
     * @return object|boolean
     */
    public static function user()
    {
        if(static::$_auth_user) {
            return static::$_auth_user;
        }

        #Check for authenticated session
        $auth_id = Session::get(static::$_auth_name);
        $instance = static::getConfig('instance');

        if($auth_id) {
            static::$_auth_user = new $instance($auth_id);
            return static::$_auth_user;
        }

        #Check for user auto log cookie
        $cookie_token = Cookie::get(static::$_auth_name);

        if($cookie_token) {
            $user_id = static::validateAuthCookieToken($cookie_token);

            if(!$user_id) {
                static::logout();
                return false;
            }

            static::$_auth_user = new $instance($user_id);
            #update cookie
            static::setUserCookieToken($user_id);
            return static::$_auth_user;
        }

        return false;
    }

    /**
     * Get Auth config
     * 
     * @param string|null $field
     * 
     * @return string[]
     */
    private static function getConfig($field = null)
    {
        if(!static::$_config) {
            static::$_config = App::getConfigByName('auth');
        }

        if(!static::$_config) {
            throw new AuthException('Auth config not found in "' . CONFIG_PATH . '"');
        }

        if($field) {
            return static::$_config[$field] ?? null;
        }

        return static::$_config;
    }

    /**
     * Create new user cookie token
     *
     * @return string Generated user token
     */
    private static function setUserCookieToken($user_id)
    {
        $cookie_table = DB::table(static::$_cookie_token_dbname);
        $token = generate_token(32);

        $cookie_table->replace([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => gettime(time() + (30 * 24 * 60 * 60))
        ]);

        Cookie::set(static::$_auth_name, $token);
        return $token;
    }

    /**
     * Create schema
     *
     * @return boolean
     */
    private static function up()
    {
        $cookie_table = DB::table(static::$_cookie_token_dbname);

        #Check if cookie table exists
        #If not create the table with it's fields
        if(!DB::hasTable(static::$_cookie_token_dbname)) {
            $cookie_table
                ->create(function ($table) {
                    $table->field('user_id')->varchar()->primary();
                    $table->field('token')->text();
                    $table->field('expires')->datetime();
                }); 
        }
        
        return true;
    }

    /**
     * Validate user's cookie
     *
     * @param string $token
     * @return boolean
     */
    private static function validateAuthCookieToken($token)
    {

        $cookie_table = DB::table(static::$_cookie_token_dbname);

        #Check if cookie table exists
        #If not create the table with it's fields
        if(!DB::hasTable(static::$_cookie_token_dbname)) {
            $cookie_table
                ->create(function ($table) {
                    $table->field('user_id')->varchar()->primary();
                    $table->field('token')->text();
                    $table->field('expires')->datetime();
                }); 
        }

        $schema_primary_key = static::getConfig('primary_key');
        $is_valid = $cookie_table
                        ->select(['user_id', 'token'])
                        ->where('token', $token)
                        ->fetchOne();

        if(!$is_valid) {
            return false;
        }

        #Update user's token to a new hash
        $new_token = static::setUserCookieToken($is_valid->user_id);

        #Set user
        return $is_valid->user_id;
    }
}