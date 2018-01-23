<?php

namespace App\Core\Misc;

use App\Core\Exceptions\FileSystemException;

class File
{

    /**
     * File resource
     * 
     * @var file
     */
    private $_file;

    /**
     * File path
     * 
     * @var string
     */
    private $_path;
    
    /**
     * File constructor
     * 
     * @param string    $path File path
     * @param bool      $create_new  Whether to create new file if not exists
     * @param bool      $throw_exists_exception Whether to throw an exception if file already exists
     */
    public function __construct($path, $create_new = false, $throw_exists_exception = false)
    {   
        $this->_path = $path;
        
        if(file_exists($this->_path) && $throw_exists_exception) {
            throw new FileSystemException
                ('File at "' . $this->_path . ' already exists"');
        }
        
        $modes = $create_new ? 'w+' : 'rw';
        $file = @fopen($path, $modes);

        if(!$file) {
            throw new FileSystemException
                ('"' . $path . '" Failed to open stream, File not found!');
        }

        $this->_file = $file;
    }

    /**
     * Class destructor
     * 
     * Close file resource on destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        
    }

    /**
     * Change file mode
     * 
     * @param int $mode New file mode
     * 
     * @return self
     */
    public function chmod($mode)
    {
        chmod($this->_path, $mode);
        return $this;
    }

    /**
     * Delete file
     * 
     * @return
     */
    public function delete()
    {
        @unlink($this->_path);
        return true;
    }

    /**
     * Return file contents
     * 
     * @return string|resource
     */
    public function getContent()
    {
        return file_get_contents($this->_path);
    }

    /**
     * Returns file resource
     * 
     * @return resource
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Rename file
     * 
     * @return string New link
     */
    public function rename($new_name)
    {
        #Close the file process to allow renaming
        $this->close();
        $filename = basename($this->_path);

        $dir = str_replace($filename, '', $this->_path);
        $new_path = "{$dir}{$new_name}";
        $rename = @rename($this->_path, $new_path);
        
        if(!$rename) {
            throw new FileSystemException
                ('Unable to rename file "' . $filename . '" to "' . $new_name . '"');
        }

        return new self($new_path);
    }

    /**
     * Write content to file
     * 
     * @param string|blob $content Content to write to file
     * 
     * @return
     */
    public function write($content)
    {
        fwrite($this->_file, $content);
        return $this;
    }

    /**
     * Close opened file resource
     * 
     * @return
     */
    private function close()
    {
        fclose($this->_file);
    }
}