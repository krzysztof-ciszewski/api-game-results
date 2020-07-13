<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Message\UpdateGameResults as UpdateGameResultsMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateGameResults implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['updateGameResults', EventPriorities::PRE_READ],
        ];
    }

    public function updateGameResults(RequestEvent $event): void
    {
        $this->messageBus->dispatch(new UpdateGameResultsMessage());
    }
}
