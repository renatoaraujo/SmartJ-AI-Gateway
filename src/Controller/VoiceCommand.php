<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Processor\Voice\VoiceCommand as Processor;

final class VoiceCommand
{
    /** @var Processor */
    private $processor;

    /**
     * VoiceCommand constructor.
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $audioFile = $request->files->get('file');

        if (!$audioFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $response = $this->processor->process($audioFile);

        return new JsonResponse($response, 200);
    }
}
