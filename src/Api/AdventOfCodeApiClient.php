<?php

declare(strict_types=1);

namespace App\Api;

use App\Date;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;

final class AdventOfCodeApiClient
{
    private Client $client;

    public function __construct(
        string $baseUrl,
        string $cookieDomain,
        ?string $sessionId,
    ) {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'cookies' => CookieJar::fromArray(['session' => $sessionId], $cookieDomain),
            'timeout' => 10
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getPuzzleInput(Date $date): string
    {
        $response = $this->client->get(sprintf('%d/day/%d/input', $date->getYearAsString(), $date->getDayAsInt()));

        return (string) $response->getBody();
    }

    /**
     * @throws GuzzleException
     */
    public function getPuzzleDescriptionPage(Date $date): string
    {
        $response = $this->client->get(sprintf('%d/day/%d', $date->getYearAsString(), $date->getDayAsInt()));

        return (string) $response->getBody();
    }

    /**
     * @throws GuzzleException
     */
    public function sendAnswer(Date $date, mixed $answer, int $part): string
    {
        $response = $this->client->post(
            sprintf('%d/day/%d/answer', $date->getYearAsString(), $date->getDayAsInt()),
            [
                'form_params' => [
                    'level' => $part,
                    'answer' => $answer,
                ]
            ]
        );

        return (string) $response->getBody();
    }
}
