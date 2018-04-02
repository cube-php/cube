<?php

namespace App\Core\Http;

use InvalidArgumentException;

use App\Core\Misc\Storage;

use App\Core\Interfaces\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{

    /**
     * Specified file
     * 
     * @var string[] $file
     */
    private $file = array();

    /**
     * Specified file size
     * 
     * @var int
     */
    private $size = 0;

    /**
     * Specified file name
     * 
     * @var string
     */
    private $name = '';

    /**
     * Specified file type
     * 
     * @var string
     */
    private $type = '';

    /**
     * File temp location
     * 
     * @var string
     */
    private $temp_loc = '';

    /**
     * File error ID
     * 
     * @var int|null
     */
    private $error = null;

    /**
     * UploadedFile Constructor
     * 
     * @param array $file
     */
    public function __construct($file)
    {
        if(!is_array($file)) {
            throw new InvalidArgumentException('Argument "$file" should be an array');
        }

        $this->attach($file);
    }

    /**
     * Retrieve the file name sent by client
     * 
     * @return string
     */
    public function getClientFilename() {

        return $this->name;
    }

    /**
     * Retrive the media type sent by the client
     * 
     * @return string
     */
    public function getClientMediaType() {

        return $this->type;
    }

    /**
     * Retrieve file error if any
     * 
     * @return int
     */
    public function getError() {

        return $this->error;
    }

    /**
     * Move file to target path
     * 
     * @param string $targetPath Path to save file to
     */
    public function moveTo($targetPath) {

        return Storage::save($this->getTempLink(), $targetPath);
    }

    /**
     * Retrieve file size
     * 
     * @return int|null
     */
    public function getSize() {

        return $this->size;
    }

    /**
     * Retrieve file temporary link
     * 
     * @return string
     */
    public function getTempLink()
    {
        return $this->temp_loc;
    }

    /**
     * Update file vars
     * 
     * @return self
     */
    private function attach($file)
    {
        $this->name = $file['name'] ?? null;
        $this->type = $file['type'] ?? null;
        $this->size = $file['size'] ?? null;
        $this->temp_loc = $file['tmp_name'] ?? null;
        $this->error = $file['error'];
    }
}