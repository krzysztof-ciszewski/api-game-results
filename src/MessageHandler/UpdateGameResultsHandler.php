<?php

namespace App\MessageHandler;

use App\ApiClient\GameResultClientInterface;
use App\Document\GameResult;
use App\Factory\GameResultFactoryInterface;
use App\Message\UpdateGameResults;
use App\Repository\GameResultRepository;
use App\Validator\Api\Exception\InvalidResponseException;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Throwable;

class UpdateGameResultsHandler implements MessageHandlerInterface
{
    /**
     * @var int
     */
    private $refreshPeriodInSec;

    /**
     * @var GameResultFactoryInterface
     */
    private $factory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GameResultRepository
     */
    private $gameRepository;

    /**
     * @var GameResultClientInterface
     */
    private $apiClient;

    public function __construct(
        GameResultRepository $gameRepository,
        int $refreshPeriodInSec,
        GameResultClientInterface $apiClient,
        GameResultFactoryInterface $factory,
        LoggerInterface $logger
    ) {
        $this->gameRepository = $gameRepository;
        $this->refreshPeriodInSec = $refreshPeriodInSec;
        $this->apiClient = $apiClient;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    public function __invoke(UpdateGameResults $message): void
    {
        /** @var GameResult[] $games */
        $games = $this->gameRepository->findBy([], ['updatedAt' => 'desc'], 1);
        $refreshDate = (new DateTime())->modify(sprintf('- %d seconds', $this->refreshPeriodInSec));
        if (!empty($games) && $games[0]->getUpdatedAt() >= $refreshDate) {
            return;
        }
        try {
            $this->gameRepository->save($this->factory->create($this->apiClient->get()));
        } catch (HttpExceptionInterface $httpException) {
            $this->logger->error(
                sprintf(
                    'Exception occurred when fetching game results from "%s"',
                    $httpException->getResponse()->getInfo('url')
                ),
                ['response' => json_encode($httpException->getResponse()->getInfo())]
            );
        } catch (InvalidResponseException $responseException) {
            $this->logger->error(
                'Received invalid response from API',
                ['content' => json_encode($responseException->getResponseBody())]
            );
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }
}
