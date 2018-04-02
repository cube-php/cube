<?php

namespace App\Core\Interfaces;

interface UploadedFileInterface
{
    public function getClientFilename();

    public function getClientMediaType();

    public function getError();

    public function getSize();

    public function moveTo($targetPath);
}