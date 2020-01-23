<?php
declare(strict_types=1);

namespace App\Processor\Voice;

use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\SpeechClient;
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

    public function process(UploadedFile $file): array
    {
        $contents = $this->uploadToStorage($file);
        return $this->speechToText($contents);
    }

    private function uploadToStorage(UploadedFile $file): string
    {
        $fileName = sprintf('%s.%s', uuid_create(UUID_TYPE_RANDOM), $file->guessExtension());

        $contents = file_get_contents($file->getRealPath());

        $this->filesystem->put($fileName, $contents);

        return $contents;
    }

    private function speechToText(string $contents): array
    {
        $audio = (new RecognitionAudio())
            ->setContent($contents);
        $config = new RecognitionConfig([
            'language_code' => 'pt-BR',
            'audio_channel_count' => 2,
        ]);

        $client = new SpeechClient();

        $response = $client->recognize($config, $audio);

        $test = [];

        foreach ($response->getResults() as $result) {
            $alternatives = $result->getAlternatives();

            foreach ($alternatives as $alternative) {
                $test[] = $alternative->getTranscript();
            }
        }

        $client->close();

        return $test;
    }
}
