<?php
declare(strict_types=1);

namespace App\Processor\Voice;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AWSVoiceCommand implements VoiceCommand
{
    /** @var FilesystemInterface */
    private $filesystem;

    /**
     * AWSVoiceCommand constructor.
     * @param FilesystemInterface $defaultStorage
     */
    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->filesystem = $defaultStorage;
    }

    public function process(UploadedFile $file): void
    {
        $fileName = sprintf('%s.%s', uuid_create(UUID_TYPE_RANDOM), $file->guessExtension());

        $contents = file_get_contents($file->getRealPath());

        $this->filesystem->put($fileName, $contents);
    }
}
