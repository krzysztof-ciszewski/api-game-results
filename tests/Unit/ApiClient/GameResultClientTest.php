<?php

namespace App\Tests\Unit\ApiClient;

use App\ApiClient\GameResultClient;
use App\Validator\Api\GameResultResponseValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GameResultClientTest extends TestCase
{
    /**
     * @dataProvider validResponseDataProvider
     */
    public function testGetValidResponse(
        string $url,
        string $uri,
        array $headers,
        HttpClientInterface $httpClient,
        GameResultResponseValidatorInterface $validator,
        LoggerInterface $logger
    ): void {
        $client = $this->getClient($httpClient, $url, $uri, $logger, $validator, $headers);
        $response = $client->get();
        $this->assertEquals(['content' => 'response'], $response);
    }

    public function validResponseDataProvider(): array
    {
        return [
            [
                'base.com//',
                '//uri/',
                [],
                $this->getHttpClientMock(
                    Request::METHOD_GET,
                    'base.com/uri',
                    ['headers' => []],
                    $this->getResponseMock(['some' => 'info'], ['content' => 'response'])
                ),
                $this->getValidatorMock(['content' => 'response']),
                $this->getLoggerMock(json_encode(['some' => 'info'])),
            ],
            [
                'base.com//',
                '//uri/',
                ['some' => 'header'],
                $this->getHttpClientMock(
                    Request::METHOD_GET,
                    'base.com/uri',
                    ['headers' => ['some' => 'header']],
                    $this->getResponseMock(['some' => 'info'], ['content' => 'response'])
                ),
                $this->getValidatorMock(['content' => 'response']),
                $this->getLoggerMock(json_encode(['some' => 'info'])),
            ],
        ];
    }

    private function getClient(
        HttpClientInterface $httpClient,
        string $apiUrl,
        string $uri,
        LoggerInterface $logger,
        GameResultResponseValidatorInterface $validator,
        array $headers = ['Accept' => 'application/json']
    ): GameResultClient {
        return new GameResultClient(
            $httpClient,
            $apiUrl,
            $uri,
            $logger,
            $validator,
            $headers
        );
    }

    private function getHttpClientMock(
        string $method,
        string $url,
        array $options,
        ResponseInterface $response,
        int $calls = 1
    ): HttpClientInterface {
        $mock = $this->prophesize(HttpClientInterface::class);
        $mock->request($method, $url, $options)->willReturn($response)->shouldBeCalledTimes($calls);

        return $mock->reveal();
    }

    private function getResponseMock(
        array $info,
        array $toArray,
        int $infoCalls = 1,
        int $toArrayCalls = 1
    ): ResponseInterface {
        $mock = $this->prophesize(ResponseInterface::class);
        $mock->getInfo()->willReturn($info)->shouldBeCalledTimes($infoCalls);
        $mock->toArray(true)->willReturn($toArray)->shouldBeCalledTimes($toArrayCalls);

        return $mock->reveal();
    }

    private function getValidatorMock(array $toValidate, int $validateCalls = 1): GameResultResponseValidatorInterface
    {
        $mock = $this->prophesize(GameResultResponseValidatorInterface::class);
        $mock->validate($toValidate)->shouldBeCalledTimes($validateCalls);

        return $mock->reveal();
    }

    private function getLoggerMock(string $message, int $infoCalls = 1): LoggerInterface
    {
        $mock = $this->prophesize(LoggerInterface::class);
        $mock->info($message)->shouldBeCalledTimes($infoCalls);

        return $mock->reveal();
    }
}
