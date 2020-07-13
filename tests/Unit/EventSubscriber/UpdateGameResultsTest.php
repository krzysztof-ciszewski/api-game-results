<?php

namespace App\Tests\Unit\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\EventSubscriber\UpdateGameResults;
use App\Message\UpdateGameResults as UpdateGameResultsMessage;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateGameResultsTest extends TestCase
{
    public function testUpdateGameResults(): void
    {
        $subscriber = new UpdateGameResults($this->getMessageBusMock());
        $subscriber->updateGameResults($this->prophesize(RequestEvent::class)->reveal());
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [
                KernelEvents::REQUEST => ['updateGameResults', EventPriorities::PRE_READ],
            ],
            UpdateGameResults::getSubscribedEvents()
        );
    }

    private function getMessageBusMock(int $dispatchCalls = 1): MessageBusInterface
    {
        $mock = $this->prophesize(MessageBusInterface::class);
        $mock
            ->dispatch(Argument::type(UpdateGameResultsMessage::class))
            ->shouldBeCalledTimes($dispatchCalls)
            ->willReturn(new Envelope(new \stdClass()));

        return $mock->reveal();
    }
}
