<?php

namespace App\ApiClient;

use App\Validator\Api\Exception\InvalidResponseException;
use App\Validator\Api\GameResultResponseValidatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameResultClient implements GameResultClientInterface
{
    /**
     * @var GameResultResponseValidatorInterface
     */
    protected $validator;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $gameResultsUri;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        HttpClientInterface $client,
        string $apiUrl,
        string $gameResultsUri,
        LoggerInterface $logger,
        GameResultResponseValidatorInterface $validator,
        array $headers = ['Accept' => 'application/json']
    ) {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->gameResultsUri = $gameResultsUri;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->headers = $headers;
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidResponseException
     */
    public function get(): array
    {
        $response = $this->client->request(Request::METHOD_GET, $this->getUrl(), ['headers' => $this->headers]);
        $this->logger->info(json_encode($response->getInfo()));
        $content = $response->toArray(true);
        $this->validator->validate($content);

        return $content;
    }

    private function getUrl(): string
    {
        return sprintf('%s/%s', trim($this->apiUrl, '/'), trim($this->gameResultsUri, '/'));
    }
}
