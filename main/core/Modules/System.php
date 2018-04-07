<?php

namespace App\Core\Modules;

use Exception;
use App\Core\Tools\Auth;
use App\Core\Helpers\Cli\Cli;
use App\Core\Modules\SessionManager;

class System
{
    /**
     * System file path
     *
     * @var string
     */
    private $_system_file_path;

    /**
     * Session
     *
     * @var Session
     */
    private $_session;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_session = new SessionManager();
        $this->_system_file_path = MAIN_APP_PATH . DS . 'core' . DS . 'system.php';
    }

    /**
     * Init system commands
     *
     * @return string
     */
    public function init()
    {
        try {
            Cli::respond('Executing system logic');
            $this->initSystemsUtilities();
            Cli::respond('System login completed');
            Cli::respond('Executing custom logic');
            $this->initCustomCommands();
            Cli::respond('Custom logic completed');
        } catch (Exception $e) {
            Cli::respond($e->getMessage(), true);
        }
            
    }

    /**
     * Load up all schemas
     *
     * @return void
     */
    public function schemas()
    {
        $tables = DB::tables();
        if(!$tables) {
            Cli::respond('No schemas created yet', true);
        }

        Cli::respond('FETCHING DATABASE SCHEMAS...');
        Cli::respond('');

        foreach($tables as $table) {
            Cli::respond($table);
        }
    }

    /**
     * Execute custom logic code
     *
     * @return mixed
     */
    private function initCustomCommands()
    {
        return require_once $this->_system_file_path;
    }

    /**
     * Initialize cubes core utilities
     *
     * @return boolean
     */
    private function initSystemsUtilities()
    {
        Auth::up();
        $this->_session->init();
    }
}