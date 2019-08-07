<?php

namespace App\Core\Helpers\Logger;

use App\Core\Interfaces\LoggerInterface;
use App\Core\Misc\File;

class Logger implements LoggerInterface
{

    /**
     * File
     *
     * @var File
     */
    private $_file;

    public function __construct()
    {
        $curdate = date('d_m_Y');
        $filename = File::joinPath(APP_LOGS_PATH, "{$curdate}.log");
        $this->_file = new File($filename, true);
    }

    /**
     * Get
     *
     * @return contents
     */
    public function get()
    {
        return $this->_file->getContent();
    }

    /**
     * Write a log
     *
     * @param string $data
     * @return bool
     */
    public function set(string $data) : bool
    {
        $content_prefix = date('[g:i:sa]');
        $content = $content_prefix . ' ' . $data . PHP_EOL;

        $this->_file->write($content);
        return true;
    }
}