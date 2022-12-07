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

        return new PuzzleMetadata(
            $puzzleInput
        );
    }
}
