<?php

namespace App\Tests\Unit\Repository;

use App\Document\GameResult;
use App\Repository\GameResultRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameResultRepositoryTest extends KernelTestCase
{
    /**
     * @dataProvider saveDataProvider
     */
    public function testSave(
        array $documents,
        int $persistCalls,
        int $flushCalls = 1,
        string $exception = null
    ): void {
        $repo = new GameResultRepository(
            $this->getManagerRegistryMock(
                $this->getDocumentManagerMock(
                    $this->getUnitOfWorkMock(),
                    $this->getClassMetadataMock(),
                    GameResult::class,
                    $persistCalls,
                    $flushCalls
                ),
                GameResult::class
            )
        );
        if ($exception) {
            $this->expectException($exception);
            $this->expectExceptionMessage(sprintf('Only "%s" documents are supported', GameResult::class));
        }
        $repo->save($documents);
    }

    public function saveDataProvider(): array
    {
        return [
            'valid documents' => [
                [
                    $this->prophesize(GameResult::class)->reveal(),
                    $this->prophesize(GameResult::class)->reveal(),
                    $this->prophesize(GameResult::class)->reveal(),
                    $this->prophesize(GameResult::class)->reveal(),
                ],
                4,
            ],
            'invalid documents' => [
                [
                    $this->prophesize(GameResult::class)->reveal(),
                    $this->prophesize(GameResult::class)->reveal(),
                    new \stdClass(),
                ],
                2,
                0,
                \InvalidArgumentException::class,
            ],
        ];
    }

    private function getManagerRegistryMock(
        DocumentManager $documentManager,
        string $class
    ): ManagerRegistry {
        $mock = $this->prophesize(ManagerRegistry::class);
        $mock->getManagerForClass($class)->willReturn($documentManager);

        return $mock->reveal();
    }

    private function getDocumentManagerMock(
        UnitOfWork $unitOfWork,
        ClassMetadata $classMetadata,
        string $class,
        int $persistCalls,
        int $flushCalls = 1
    ): DocumentManager {
        $mock = $this->prophesize(DocumentManager::class);
        $mock->getUnitOfWork()->willReturn($unitOfWork);
        $mock->getClassMetadata($class)->willReturn($classMetadata);
        $mock->persist(Argument::type(GameResult::class))->shouldBeCalledTimes($persistCalls);
        $mock->flush()->shouldBeCalledTimes($flushCalls);

        return $mock->reveal();
    }

    private function getUnitOfWorkMock(): UnitOfWork
    {
        return $this->prophesize(UnitOfWork::class)->reveal();
    }

    private function getClassMetadataMock(): ClassMetadata
    {
        return $this->prophesize(ClassMetadata::class)->reveal();
    }
}
