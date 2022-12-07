<?php

declare(strict_types=1);

namespace App\Services;

use App\Api\AdventOfCodeApiClient;
use App\Date;
use App\Exceptions\ApiException;
use App\Exceptions\UploadAnswerError;
use App\Result;
use GuzzleHttp\Exception\GuzzleException;

class AnswersService
{
    public function __construct(
        private readonly AdventOfCodeApiClient $client
    ) {
    }

    /**
     * @throws ApiException
     */
    public function fetchAnswers(Date $date): Result
    {
        try {
            $puzzleDescriptionPageContent = $this->client->getPuzzleDescriptionPage($date);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage());
        }

        return $this->getResult($puzzleDescriptionPageContent);
    }

    /**
     * @throws ApiException
     * @throws UploadAnswerError
     */
    public function uploadAnswer(Date $date, mixed $answer, int $level): bool
    {
        try {
            $response = $this->client->sendAnswer($date, $answer, $level);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage());
        }

        if (str_contains($response, 'You gave an answer too recently')) {
            throw new UploadAnswerError('You gave an answer too recently. You have to wait one minute after submitting an answer before trying again');
        }

        if (str_contains($response, "You don't seem to be solving the right level.  Did you already complete it?")) {
            throw new UploadAnswerError('You seem to be uploading already completed level. Level: ' . $level);
        }

        if (str_contains($response, "That's not the right answer")) {
            return false;
        }

        return true;
    }

    private function getResult(string $puzzleDescriptionPageContent): Result
    {
        if (preg_match_all('/.*?Your puzzle answer was \<code\>(.*?)\<\/code\>/is', $puzzleDescriptionPageContent, $matches)) {
            return Result::fromArray($matches[1] ?? []);
        }

        return new Result();
    }
}
