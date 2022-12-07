<?php

declare(strict_types=1);

namespace App\Services;

use App\Api\AdventOfCodeApiClient;
use App\Date;
use App\Exceptions\ApiException;
use App\PuzzleMetadata;
use GuzzleHttp\Exception\GuzzleException;

final class PuzzleMetadataFetcher
{
    public function __construct(
        private readonly AdventOfCodeApiClient $client
    ) {
    }

    /**
     * @throws ApiException
     */
    public function fetch(Date $date): PuzzleMetadata
    {
        try {
            $puzzleInput = $this->client->getPuzzleInput($date);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage());
        }

        try {
            $puzzleDescriptionPageContent = $this->client->getPuzzleDescriptionPage($date);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage());
        }

        $puzzleName = $this->getPuzzleName($puzzleDescriptionPageContent);

        return new PuzzleMetadata(
            $puzzleInput,
            $puzzleName
        );
    }

    private function getPuzzleName(string $puzzleDescriptionPageContent): string
    {
        if (preg_match("/--- Day \d+: (.*) ---/", $puzzleDescriptionPageContent, $matches)) {
            return $matches[1];
        }

        throw new \RuntimeException('Unable to scrap puzzle name');
    }
}
