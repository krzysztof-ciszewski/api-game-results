<?php

namespace App\Tests\Unit\MessageHandler;

use App\ApiClient\GameResultClientInterface;
use App\Document\GameResult;
use App\Factory\GameResultFactoryInterface;
use App\Message\UpdateGameResults;
use App\MessageHandler\UpdateGameResultsHandler;
use App\Repository\GameResultRepository;
use App\Validator\Api\Exception\InvalidResponseException;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class UpdateGameResultsHandlerTest extends TestCase
{
    /**
     * @dataProvider noExceptionDataProvider
     */
    public function testHandlerNoExceptions(
        array $newestGames,
        array $newDocuments,
        array $response,
        int $refreshPeriodInSec,
        int $saveCalls,
        int $createCalls,
        int $getCalls
    ): void {
        $handler = new UpdateGameResultsHandler(
            $this->getGameResultRepositoryMock($newestGames, $newDocuments, $saveCalls),
            $refreshPeriodInSec,
            $this->getGameResultClientMock($response, $getCalls),
            $this->getFactoryMock($response, $newDocuments, $createCalls),
            $this->getLoggerMock()
        );
        $handler(new UpdateGameResults());
    }

    /**
     * @dataProvider withExceptionsDataProvider
     */
    public function testHandlerWithExceptions(
        int $saveCalls,
        int $createCalls,
        int $getCalls,
        string $loggerMessage,
        array $loggerContext,
        ?Throwable $apiClientException,
        ?Throwable $factoryException,
        ?Throwable $repoException
    ): void {
        $newestGames = [];
        $newDocuments = ['documents'];
        $response = ['response'];
        $refreshPeriodInSec = 60;
        $handler = new UpdateGameResultsHandler(
            $this->getGameResultRepositoryMock($newestGames, $newDocuments, $saveCalls, $repoException),
            $refreshPeriodInSec,
            $this->getGameResultClientMock($response, $getCalls, $apiClientException),
            $this->getFactoryMock($response, $newDocuments, $createCalls, $factoryException),
            $this->getLoggerMock($loggerMessage, $loggerContext)
        );
        $handler(new UpdateGameResults());
    }

    public function noExceptionDataProvider(): array
    {
        return [
            'results recently updated' => [
                [
                    $this->getGameResultMock(new DateTime()),
                ],
                [],
                [],
                60,
                0,
                0,
                0,
            ],
            'results empty, need to be updated' => [
                [],
                ['newdocuments'],
                ['response'],
                60,
                1,
                1,
                1,
            ],
            'results updated before refresh date' => [
                [
                    $this->getGameResultMock((new DateTime())->modify('- 100 seconds')),
                ],
                ['newdocuments'],
                ['response'],
                60,
                1,
                1,
                1,
            ],
        ];
    }

    public function withExceptionsDataProvider(): array
    {
        $genericException = new \Exception('message');

        return [
            'api client throws exception' => [
                $saveCalls = 0,
                $createCalls = 0,
                $getCalls = 1,
                'message',
                ['exception' => $genericException],
                $genericException,
                null,
                null,
            ],
            'factory throws exception' => [
                $saveCalls = 0,
                $createCalls = 1,
                $getCalls = 1,
                'message',
                ['exception' => $genericException],
                null,
                $genericException,
                null,
            ],
            'repository throws exception' => [
                $saveCalls = 1,
                $createCalls = 1,
                $getCalls = 1,
                'message',
                ['exception' => $genericException],
                null,
                null,
                $genericException,
            ],
            'HttpExceptionInterface is thrown' => [
                $saveCalls = 0,
                $createCalls = 0,
                $getCalls = 1,
                'Exception occurred when fetching game results from "url"',
                ['response' => json_encode(['some' => 'info'])],
                $this->getHttpExceptionMock('url', ['some' => 'info']),
                null,
                null,
            ],
            'InvalidResponseException is thrown' => [
                $saveCalls = 0,
                $createCalls = 0,
                $getCalls = 1,
                'Received invalid response from API',
                ['content' => json_encode(['response'])],
                new InvalidResponseException(['response']),
                null,
                null,
            ],
        ];
    }

    private function getGameResultRepositoryMock(
        array $newestGames,
        array $newDocuments,
        int $saveCalls,
        ?Throwable $exception = null
    ): GameResultRepository {
        $mock = $this->prophesize(GameResultRepository::class);
        $mock->findBy([], ['updatedAt' => 'desc'], 1)->willReturn($newestGames);
        $mock->save($newDocuments)->shouldBeCalledTimes($saveCalls);
        if ($exception) {
            $mock->getMethodProphecies('save')[0]->willThrow($exception);
        }

        return $mock->reveal();
    }

    private function getGameResultClientMock(
        array $response,
        int $getCalls,
        ?Throwable $exception = null
    ): GameResultClientInterface {
        $mock = $this->prophesize(GameResultClientInterface::class);
        $mock->get()->willReturn($response)->shouldBeCalledTimes($getCalls);
        if ($exception) {
            $mock->get()->willThrow($exception);
        }

        return $mock->reveal();
    }

    private function getFactoryMock(
        array $results,
        array $documents,
        int $createCalls,
        ?Throwable $exception = null
    ): GameResultFactoryInterface {
        $mock = $this->prophesize(GameResultFactoryInterface::class);
        $mock->create($results)->willReturn($documents)->shouldBeCalledTimes($createCalls);
        if ($exception) {
            $mock->getMethodProphecies('create')[0]->willThrow($exception);
        }

        return $mock->reveal();
    }

    private function getLoggerMock(string $message = null, array $context = null): LoggerInterface
    {
        $mock = $this->prophesize(LoggerInterface::class);
        if ($message && $context) {
            $mock->error($message, $context)->shouldBeCalledOnce();
        }

        return $mock->reveal();
    }

    private function getGameResultMock(DateTime $updatedAt): GameResult
    {
        $mock = $this->prophesize(GameResult::class);
        $mock->getUpdatedAt()->willReturn($updatedAt);

        return $mock->reveal();
    }

    private function getHttpExceptionMock(string $url, array $info): HttpExceptionInterface
    {
        $exception = $this->prophesize(HttpExceptionInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $response->getInfo()->willReturn($info);
        $response->getInfo('url')->willReturn($url);
        $exception->getResponse()->willReturn($response->reveal());

        return $exception->reveal();
    }
}
