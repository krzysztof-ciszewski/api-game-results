<?php

namespace App\Tests\Unit\Document;

use App\Document\GameResult;
use App\Document\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class GameResultTest extends TestCase
{
    /**
     * @dataProvider constructorDataProvider
     */
    public function testConstruct(string $id, int $score, User $user, DateTime $finishedAt): void
    {
        $document = new GameResult($id, $score, $user, $finishedAt);
        $this->assertSame($id, $document->getId());
        $this->assertSame($score, $document->getScore());
        $this->assertSame($user->getId(), $document->getUser()->getId());
        $this->assertSame($user->getName(), $document->getUser()->getName());
        $this->assertSame($finishedAt, $document->getFinishedAt());
        $this->assertNull($document->getUpdatedAt());
    }

    /**
     * @dataProvider constructorDataProvider
     */
    public function testSetters(string $id, int $score, User $user, DateTime $finishedAt): void
    {
        $newId = $id.'1';
        $newScore = $score + 1;
        $newUser = new User($user->getId().'2', $user->getName().'2');
        $newFinishedAt = clone $finishedAt;
        $newFinishedAt->modify('+ 1 minute');
        $updateAt = new DateTime();
        $document = new GameResult($id, $score, $user, $finishedAt);
        $document->setId($newId);
        $document->setScore($newScore);
        $document->setUser($newUser);
        $document->setFinishedAt($newFinishedAt);
        $document->setUpdatedAt($updateAt);
        $this->assertSame($newId, $document->getId());
        $this->assertSame($newScore, $document->getScore());
        $this->assertSame($newFinishedAt, $document->getFinishedAt());
        $this->assertSame($updateAt, $document->getUpdatedAt());
        $this->assertSame($newUser->getId(), $document->getUser()->getId());
        $this->assertSame($newUser->getName(), $document->getUser()->getName());
    }

    public function constructorDataProvider(): array
    {
        return [
            ['id', 1, new User('userid', 'some name'), new DateTime()],
        ];
    }
}
