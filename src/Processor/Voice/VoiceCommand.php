<?php

namespace App\Processor\Voice;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface VoiceCommand
{
    public function process(UploadedFile $file): void;
}
